<?php

namespace App\Controller;

use App\Entity\Race;
use App\Repository\RaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use OpenApi\Annotations as OA;

#[Route('api/race', name:'app_api_race_')]
class RaceController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager, 
        private RaceRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    /**
     * @OA\Post(
     *     path="/api/race",
     *     summary="Ajouter une race",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Ajouter une race",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="label", type="string", example="Race de chien")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Race enregistrée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="label", type="string", example="Race de chien")
     *         )
     *     )
     * )
     */
    #[Route(methods: 'POST')]
    public function new(Request $request): JsonResponse
    {
        $race = $this->serializer->deserialize(
            $request->getContent(),
            Race::class,
            'json',
            ['groups' => ['race:read', 'race:write']]
        );
    
        $this->manager->persist($race);
        $this->manager->flush();
    
        $responseData = $this->serializer->serialize($race, 'json', ['groups' => ['race:read']]);
        $location = $this->urlGenerator->generate(
            'app_api_race_show',
            ['id' => $race->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    /**
     * @OA\Get(
     *     path="/api/race/{id}",
     *     summary="Obtenir une race par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la race"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails de la race",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="label", type="string", example="Race de chien")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Race non trouvée"
     *     )
     * )
     */
    #[Route('/{id}', name: 'show', methods: 'GET')]
    public function show(int $id): JsonResponse
    {
        $race = $this->repository->findOneBy(['id' => $id]);

        if (!$race) {
            return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
        }

        $responseData = $this->serializer->serialize($race, 'json', ['groups' => ['race:read']]);
        return new JsonResponse(data: $responseData, status: Response::HTTP_OK, json: true);
    }

    // DOCUMENTATION
    #[Route(methods: 'GET')]
    public function showAll(): JsonResponse
    {
        $race = $this->repository->findAll();

        if (!$race) {
            return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
        }

        $responseData = $this->serializer->serialize($race, 'json', ['groups' => ['race:read']]);
        return new JsonResponse(data: $responseData, status: Response::HTTP_OK, json: true);
    }

    /**
     * @OA\Put(
     *     path="/api/race/{id}",
     *     summary="Mettre à jour une race par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la race"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Les détails de la race à mettre à jour",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="label", type="string", example="Race de chien")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Race mise à jour avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Race non trouvée"
     *     )
     * )
     */
    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $race = $this->repository->findOneBy(['id' => $id]);
        if (!$race) {
            return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $race->setLabel($data['label']);

        $this->manager->flush();
        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Delete(
     *     path="/api/race/{id}",
     *     summary="Supprimer une race par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la race"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Race supprimée avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Race non trouvée"
     *     )
     * )
     */
    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $race = $this->repository->findOneBy(['id' => $id]);
        if (!$race) {
            return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
        }

        $this->manager->remove($race);
        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }
}
