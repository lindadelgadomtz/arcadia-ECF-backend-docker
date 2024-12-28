<?php

namespace App\Controller;

use App\Entity\Habitat;
use App\Repository\HabitatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use OpenApi\Annotations as OA;

#[Route('api/habitat', name: 'app_api_habitat_')]
class HabitatController extends AbstractController
{
    private HabitatRepository $repository;
    private SerializerInterface $serializer;
    private EntityManagerInterface $manager;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(HabitatRepository $repository, SerializerInterface $serializer, EntityManagerInterface $manager, UrlGeneratorInterface $urlGenerator)
    {
        $this->repository = $repository;
        $this->serializer = $serializer;
        $this->manager = $manager;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @OA\Post(
     *     path="/api/habitat",
     *     summary="Create a new habitat",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="nom", type="string", example="Forest"),
     *             @OA\Property(property="description", type="string", example="A large forest"),
     *             @OA\Property(property="commentaire_habitat", type="string", example="This is a comment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Habitat created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nom", type="string", example="Forest"),
     *             @OA\Property(property="description", type="string", example="A large forest"),
     *             @OA\Property(property="commentaire_habitat", type="string", example="This is a comment")
     *         )
     *     )
     * )
     */

    #[Route(methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $habitat = $this->serializer->deserialize($request->getContent(), Habitat::class, 'json');

        $this->manager->persist($habitat);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($habitat, 'json', [
            AbstractNormalizer::GROUPS => ['habitat:read']
        ]);
        $location = $this->urlGenerator->generate(
            'app_api_habitat_show',
            ['id' => $habitat->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }


     /**
     * @OA\Get(
     *     path="/api/habitat/{id}",
     *     summary="Get habitat by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the habitat"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Habitat details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nom", type="string", example="Forest"),
     *             @OA\Property(property="description", type="string", example="A large forest"),
     *             @OA\Property(property="commentaire_habitat", type="string", example="This is a comment"),
     *             @OA\Property(property="gallery_ids", type="array", @OA\Items(type="integer")),
     *             @OA\Property(property="animal_ids", type="array", @OA\Items(type="integer")), 
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Habitat not found"
     *     )
     * )
     */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $habitat = $this->repository->findOneBy(['id' => $id]);

        if (!$habitat) {
            return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
        }

                // Get gallery IDs
                $galleryIds = [];
                foreach ($habitat->getGallery() as $gallery) {
                    $galleryIds[] = $gallery->getId();
                }
        
                // Prepare the response data including the gallery IDs
        $responseData = $this->serializer->serialize($habitat, 'json', [
            AbstractNormalizer::GROUPS => ['habitat:read', 'habitat:write'],
        ]);

        // Decode the serialized data to add gallery_ids
        $responseArray = json_decode($responseData, true);
        $responseArray['gallery'] = $galleryIds;

        return new JsonResponse(data: $responseArray, status: Response::HTTP_OK);
    }


    //  /**
    //  * @OA\Get(
    //  *     path="/api/habitat",
    //  *     summary="Get all habitats by ID",
    //  *     @OA\Parameter(
    //  *         name="id",
    //  *         in="path",
    //  *         required=true,
    //  *         @OA\Schema(type="integer"),
    //  *         description="ID of the habitats"
    //  *     ),
    //  *     @OA\Response(
    //  *         response=200,
    //  *         description="All habitat details",
    //  *         @OA\JsonContent(
    //  *             type="object",
    //  *             @OA\Property(property="id", type="integer", example=1),
    //  *             @OA\Property(property="nom", type="string", example="Forest"),
    //  *             @OA\Property(property="description", type="string", example="A large forest"),
    //  *             @OA\Property(property="commentaire_habitat", type="string", example="This is a comment"),
    //  *             @OA\Property(property="gallery_ids", type="array", @OA\Items(type="integer")),
    //  *             @OA\Property(property="animal_ids", type="array", @OA\Items(type="integer")), 
    //  *         )
    //  *     ),
    //  *     @OA\Response(
    //  *         response=404,
    //  *         description="Habitats not found"
    //  *     )
    //  * )
    //  */


    #[Route(methods: 'GET')]
    public function showAll(): JsonResponse
    {
        
        $habitats = $this->repository->findAll();

        if (!$habitats) {
            return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
        }

        $responseArray = [];
        foreach ($habitats as $habitat) {
            // Get gallery IDs for each habitat
            $galleryIds = [];
            foreach ($habitat->getGallery() as $gallery) {
                $galleryIds[] = $gallery->getUrlImage();
            }

            // Serialize each habitat
            $serializedHabitat = $this->serializer->serialize($habitat, 'json', [
                AbstractNormalizer::GROUPS => ['habitat:read', 'habitat:write'], 
            ]);

            // Decode serialized data to add gallery_ids and custom fields
            $habitatArray = json_decode($serializedHabitat, true);
            $habitatArray['gallery'] = $galleryIds;
            $habitatArray['url_image'] = $habitat->getGallery();

            $responseArray[] = $habitatArray;
        }

        return new JsonResponse(data: $responseArray, status: Response::HTTP_OK);
    }


    /**
     * @OA\Put(
     *     path="/api/habitat/{id}",
     *     summary="Update habitat by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the habitat"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="nom", type="string", example="Forest"),
     *             @OA\Property(property="description", type="string", example="A large forest"),
     *             @OA\Property(property="commentaire_habitat", type="string", example="This is a comment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Habitat updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Habitat not found"
     *     )
     * )
     */
    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    public function edit(int $id, Request $request): JsonResponse
    {
        $habitat = $this->repository->findOneBy(['id' => $id]);
        if (!$habitat) {
            return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $habitat->setNom($data['nom'] ?? $habitat->getNom());
        $habitat->setDescription($data['description'] ?? $habitat->getDescription());
        $habitat->setCommentaireHabitat($data['commentaire_habitat'] ?? $habitat->getCommentaireHabitat());

        $this->manager->flush();
        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }


    /**
     * @OA\Delete(
     *     path="/api/habitat/{id}",
     *     summary="Delete habitat by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the habitat"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Habitat deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Habitat not found"
     *     )
     * )
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $habitat = $this->repository->findOneBy(['id' => $id]);
        if (!$habitat) {
            return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
        }

        $this->manager->remove($habitat);
        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }
}
