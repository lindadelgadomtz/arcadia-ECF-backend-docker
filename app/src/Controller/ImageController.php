<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Animal;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use DateTimeImmutable;
use OpenApi\Annotations as OA;

#[Route('api/image', name:'app_api_image_')]
class ImageController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager, 
        private ImageRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    /**
     * @OA\Post(
     *     path="/api/image",
     *     summary="Ajouter une image",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Ajouter une image",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="image_data", type="string", format="binary"),
     *             @OA\Property(property="habitat", type="integer", example=1),
     *             @OA\Property(property="animal", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Image enregistrée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="image_data", type="string", format="binary"),
     *             @OA\Property(property="habitat", type="integer", example=1),
     *             @OA\Property(property="animal", type="array", @OA\Items(type="integer"))
     *         )
     *     )
     * )
     */
    #[Route(methods: 'POST')]
    public function new(Request $request): JsonResponse
    {
        $image = $this->serializer->deserialize(
            $request->getContent(),
            Image::class,
            'json'
        );
        $image->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($image);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($image, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_image_show',
            ['id' => $image->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    /**
     * @OA\Get(
     *     path="/api/image/{id}",
     *     summary="Obtenir une image par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de l'image"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails de l'image",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="image_data", type="string", format="binary"),
     *             @OA\Property(property="habitat", type="integer", example=1),
     *             @OA\Property(property="animal", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Image non trouvée"
     *     )
     * )
     */
    #[Route('/{id}', name: 'show', methods: 'GET')]
    public function show(int $id): JsonResponse
    {
        $image = $this->repository->findOneBy(['id' => $id]);

        if (!$image) {
            return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
        }

        $responseData = $this->serializer->serialize($image, 'json');
        return new JsonResponse(data: $responseData, status: Response::HTTP_OK, json: true);
    }

    /**
     * @OA\Put(
     *     path="/api/image/{id}",
     *     summary="Mettre à jour une image par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de l'image"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Les détails de l'image à mettre à jour",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="image_data", type="string", format="binary"),
     *             @OA\Property(property="habitat", type="integer", example=1),
     *             @OA\Property(property="animal", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Image mise à jour avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Image non trouvée"
     *     )
     * )
     */
    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $image = $this->repository->findOneBy(['id' => $id]);
        if (!$image) {
            return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $image->setImageData($data['image_data'] ?? $image->getImageData());

        if (isset($data['habitat'])) {
            $image->setHabitat($data['habitat']);
        }

        if (isset($data['animal'])) {
            foreach ($data['animal'] as $animalId) {
                $animal = $this->manager->getRepository(Animal::class)->find($animalId);
                if ($animal) {
                    $image->addAnimal($animal);
                }
            }
        }

        $this->manager->flush();
        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Delete(
     *     path="/api/image/{id}",
     *     summary="Supprimer une image par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de l'image"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Image supprimée avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Image non trouvée"
     *     )
     * )
     */
    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $image = $this->repository->findOneBy(['id' => $id]);
        if (!$image) {
            return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
        }

        $this->manager->remove($image);
        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }
}
