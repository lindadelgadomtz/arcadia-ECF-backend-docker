<?php

namespace App\Controller;

use App\Entity\AnimalFeeding;
use App\Repository\AnimalFeedingRepository;
use App\Repository\AnimalRepository;
use App\Repository\UserRepository;
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


#[Route('api/animalFeeding', name: 'app_api_animalFeeding_')]
class AnimalFeedingController extends AbstractController
{
    private AnimalFeedingRepository $repository;
    // private UserRepository $userRepository;
    private AnimalRepository $animalRepository;
    private SerializerInterface $serializer;
    private UrlGeneratorInterface $urlGenerator;
    private EntityManagerInterface $manager;

    public function __construct(AnimalFeedingRepository $repository, AnimalRepository $animalRepository, SerializerInterface $serializer, EntityManagerInterface $manager, UrlGeneratorInterface $urlGenerator)
    {
        $this->repository = $repository;
        $this->animalRepository = $animalRepository;
        // $this->userRepository = $userRepository;
        $this->serializer = $serializer;
        $this->manager = $manager;
        $this->urlGenerator = $urlGenerator;
        $this->animalRepository = $animalRepository;
    }

    /**
     * @OA\Post(
     *     path="/api/animalFeeding",
     *     summary="Create a new animal feeding",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="date", type="string", format="date", example="2024-07-18"),
     *             @OA\Property(property="nourriture", type="string", example="Nourriture example"),
     *             @OA\Property(property="nourriture_grammage_emp", type="int", example="500"),
     *             @OA\Property(
     *                 property="animal",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=8)
     *             ),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Animal Feeding created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="date", type="string", format="date", example="2024-07-18"),
     *             @OA\Property(property="nourriture", type="string", example="Nourriture example"),
     *             @OA\Property(property="nourriture_grammage_emp", type="int", example="500"),
     *             @OA\Property(
     *                 property="animal",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=8)
     *             ),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1)
     *             )
     *         )
     *     )
     * )
     */
    #[Route('', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $nourriture = $data['nourriture'] ?? null;
        $nourritureGrammageEmp = $data['nourriture_grammage_emp'] ?? null;
        $animalId = $data['animal']['id'] ?? null;
        // $userId = $data['user']['id'] ?? null;

        if (!$nourriture || !$nourritureGrammageEmp || !$animalId) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $animalFeeding = new AnimalFeeding();
        $animalFeeding->setNourriture($nourriture);
        $animalFeeding->setNourritureGrammageEmp($nourritureGrammageEmp);
        $animalFeeding->setDate(new \DateTimeImmutable($data['date']));

        $animal = $this->animalRepository->find($animalId);
        // $user = $this->userRepository->find($userId);

        // if (!$animal || !$user) {
        //     return new JsonResponse(['error' => 'Invalid animal or user ID'], Response::HTTP_BAD_REQUEST);
        // }

        $animal = $this->animalRepository->find($data['animal']['id']);
        if ($animal) {
        $animalFeeding->addAnimal($animal);  // Correct way to add animal
        }
        // $animalFeeding->addUser($user);

        $this->manager->persist($animalFeeding);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($animalFeeding, 'json', [
            AbstractNormalizer::GROUPS => ['animalFeeding:read']
        ]);

        $location = $this->urlGenerator->generate(
            'app_api_animalFeeding_show',
            ['id' => $animalFeeding->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    /**
     * @OA\Get(
     *     path="/api/animalFeeding/{id}",
     *     summary="Get animalFeeding by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the animal Feeding"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Animal details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="date", type="string", format="date", example="2024-07-18"),
     *             @OA\Property(property="nourriture", type="string", example="Nourriture example"),
     *             @OA\Property(property="nourriture_grammage_emp", type="int", example="500"),
     *             @OA\Property(
     *                 property="animal",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=8)
     *             ),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Animal Feeding not found"
     *     )
     * )
     */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $animalFeeding = $this->repository->findOneBy(['id' => $id]);

        if (!$animalFeeding) {
            return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
        }

        $responseData = $this->serializer->serialize($animalFeeding, 'json', [
            AbstractNormalizer::GROUPS => ['animalFeeding:read']
        ]);
        
        return new JsonResponse(data: $responseData, status: Response::HTTP_OK, json: true);
    }

/**
     * @OA\Get(
     *     path="/api/animalFeeding/animal/{id}",
     *     summary="Get animalFeeding by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the animal Feeding"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Animal details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="date", type="string", format="date", example="2024-07-18"),
     *             @OA\Property(property="nourriture", type="string", example="Nourriture example"),
     *             @OA\Property(property="nourriture_grammage_emp", type="int", example="500"),
     *             @OA\Property(
     *                 property="animal",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=8)
     *             ),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Animal Feeding not found"
     *     )
     * )
     */
    #[Route(methods: 'GET')]
    public function showAll(): JsonResponse
    {
        $animalFeeding = $this->repository->findAll();

        if (!$animalFeeding) {
            return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
        }

        $responseData = $this->serializer->serialize($animalFeeding, 'json', [
            AbstractNormalizer::GROUPS => ['animalFeeding:read']
        ]);
        
        return new JsonResponse(data: $responseData, status: Response::HTTP_OK, json: true);
    }

    /**
     * @OA\Put(
     *     path="/api/animalFeeding/{id}",
     *     summary="Update animal Feeding by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the animal Feeding"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="date", type="string", format="date", example="2024-07-18"),
     *             @OA\Property(property="nourriture", type="string", example="Nourriture example"),
     *             @OA\Property(property="nourriture_grammage_emp", type="int", example="500"),
     *             @OA\Property(
     *                 property="animal",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=8)
     *             ),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1)
     *             )
     *          )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Animal Feeding updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Animal Feeding not found"
     *     )
     * )
     */
    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    public function edit(int $id, Request $request): JsonResponse
    {
        $animalFeeding = $this->repository->find($id);
        if (!$animalFeeding) {
            return new JsonResponse(['error' => 'Animal Feeding not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $nourriture = $data['nourriture'] ?? null;
        $nourritureGrammageEmp = $data['nourriture_grammage_emp'] ?? null;
        $animalId = $data['animal']['id'] ?? null;
        // $userId = $data['user']['id'] ?? null;

        if (!$nourriture || !$nourritureGrammageEmp || !$animalId) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $animalFeeding->setNourriture($nourriture);
        $animalFeeding->setNourritureGrammageEmp($nourritureGrammageEmp);
        $animalFeeding->setDate(new \DateTimeImmutable($data['date']));

        $animal = $this->animalRepository->find($animalId);
        // $user = $this->userRepository->find($userId);

        if (!$animal) {
            return new JsonResponse(['error' => 'Invalid animal or user ID'], Response::HTTP_BAD_REQUEST);
        }

        $animal = $this->animalRepository->find($data['animal']['id']);
        if ($animal) {
        $animalFeeding->addAnimal($animal);  // Correct way to add animal
        }
        // $animalFeeding->addUser($user);

        $this->manager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Delete(
     *     path="/api/animalFeeding/{id}",
     *     summary="Delete animal feeding by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the animal feeding"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Animal feeding deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Animal feeding not found"
     *     )
     * )
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $animalFeeding = $this->repository->find($id);
        if (!$animalFeeding) {
            return new JsonResponse(['error' => 'Animal Feeding not found'], Response::HTTP_NOT_FOUND);
        }

        $this->manager->remove($animalFeeding);
        $this->manager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
