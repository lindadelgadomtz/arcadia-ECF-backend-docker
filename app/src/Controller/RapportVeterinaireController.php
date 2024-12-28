<?php

namespace App\Controller;

use App\Entity\RapportVeterinaire;
use App\Repository\RapportVeterinaireRepository;
use App\Repository\AnimalRepository;
use App\Repository\AnimalFeedingRepository;
use App\Repository\RaceRepository;
use App\Repository\HabitatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface; // or DenormalizerInterface if needed


#[Route('api/rapportVeterinaire', name: 'app_api_rapportVeterinaire_')]
class RapportVeterinaireController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private RapportVeterinaireRepository $raportVeterinaireRepository,
        private AnimalRepository $animalRepository,
        private AnimalFeedingRepository $animalFeedingRepository,
        private RaceRepository $raceRepository,
        private HabitatRepository $habitatRepository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        private NormalizerInterface $normalizer, // or DenormalizerInterface if needed
    ) {
    }

    /**
     * @OA\Post(
     *     path="/api/rapportVeterinaire",
     *     summary="Ajouter un rapport vétérinaire",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Les détails du rapport vétérinaire",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="date", type="string", format="date", example="2024-07-18"),
     *             @OA\Property(property="detail", type="string", example="Détails du rapport"),
     *             @OA\Property(property="animal", type="object", example={"id": 1}),
     *             @OA\Property(property="etat_animal", type="string", example="État de l'animal"),
     *             @OA\Property(property="nourriture", type="string", example="Type de nourriture"),
     *             @OA\Property(property="nourriture_grammage", type="integer", example=500)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Rapport vétérinaire enregistré avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="date", type="string", format="date", example="2024-07-18"),
     *             @OA\Property(property="detail", type="string", example="Détails du rapport"),
     *             @OA\Property(property="animal", type="object", example={"id": 1}),
     *             @OA\Property(property="etat_animal", type="string", example="État de l'animal"),
     *             @OA\Property(property="nourriture", type="string", example="Type de nourriture"),
     *             @OA\Property(property="nourriture_grammage", type="integer", example=500)
     *         )
     *     )
     * )
     */
    #[Route(methods: ['POST'])]
public function new(Request $request): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    // Fetch the Animal based on the provided ID
    $animalId = $data['animal']['id'];
    $animal = $this->animalRepository->find($animalId);

    if (!$animal) {
        return new JsonResponse(['error' => 'Animal not found'], Response::HTTP_BAD_REQUEST);
    }

    // No need to handle Race and Habitat here, they are already associated with the Animal
    // Simply create the RapportVeterinaire and associate it with the existing Animal

    $rapportVeterinaire = new RapportVeterinaire();
    $rapportVeterinaire->setDate(new \DateTimeImmutable($data['date']));
    $rapportVeterinaire->setDetail($data['detail']);
    $rapportVeterinaire->setEtatAnimal($data['etat_animal']);
    $rapportVeterinaire->setNourriture($data['nourriture']);
    $rapportVeterinaire->setNourritureGrammage($data['nourriture_grammage']);
    $rapportVeterinaire->setAnimal($animal);

    $this->manager->persist($rapportVeterinaire);
    $this->manager->flush();

    $responseData = $this->serializer->serialize($rapportVeterinaire, 'json', [
        'groups' => ['rapportVeterinaire:read']
    ]);
    $location = $this->urlGenerator->generate(
        'app_api_rapportVeterinaire_show',
        ['id' => $rapportVeterinaire->getId()],
        UrlGeneratorInterface::ABSOLUTE_URL
    );
    return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
}
    /**
     * @OA\Get(
     *     path="/api/rapportVeterinaire/{id}",
     *     summary="Obtenir un rapport vétérinaire par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID du rapport vétérinaire"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails du rapport vétérinaire",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="date", type="string", format="date", example="2024-07-18"),
     *             @OA\Property(property="detail", type="string", example="Détails du rapport"),
     *             @OA\Property(property="animal", type="object", example={"id": 1}),
     *             @OA\Property(property="etat_animal", type="string", example="État de l'animal"),
     *             @OA\Property(property="nourriture", type="string", example="Type de nourriture"),
     *             @OA\Property(property="nourriture_grammage", type="integer", example=500)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rapport vétérinaire non trouvé"
     *     )
     * )
     */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $rapportVeterinaire = $this->raportVeterinaireRepository->findOneBy(['id' => $id]);

        if (!$rapportVeterinaire) {
            return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
        }

        $responseData = $this->serializer->serialize($rapportVeterinaire, 'json');
        return new JsonResponse(data: $responseData, status: Response::HTTP_OK, json: true);
    }

#[Route('/getAnimalIdByName/{prenom}', methods: ['GET'])]
public function getAnimalIdByName(string $prenom): JsonResponse
{
    $animal = $this->animalRepository->findOneBy(['prenom' => $prenom]);

    if (!$animal) {
        return new JsonResponse(['error' => 'Animal not found'], JsonResponse::HTTP_NOT_FOUND);
    }

    return new JsonResponse(['id' => $animal->getId(), 'name' => $animal->getPrenom()]);
}

    /**
     * @OA\Put(
     *     path="/api/rapportVeterinaire/{id}",
     *     summary="Mettre à jour un rapport vétérinaire par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID du rapport vétérinaire"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Détails du rapport vétérinaire à mettre à jour",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="date", type="string", format="date", example="2024-07-18"),
     *             @OA\Property(property="detail", type="string", example="Détails du rapport"),
     *             @OA\Property(property="animal", type="object", example={"id": 1}),
     *             @OA\Property(property="etat_animal", type="string", example="État de l'animal"),
     *             @OA\Property(property="nourriture", type="string", example="Type de nourriture"),
     *             @OA\Property(property="nourriture_grammage", type="integer", example=500)
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Rapport vétérinaire mis à jour avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rapport vétérinaire non trouvé"
     *     )
     * )
     */
    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    public function edit(int $id, Request $request): JsonResponse
    {
        $rapportVeterinaire = $this->raportVeterinaireRepository->findOneBy(['id' => $id]);
        if (!$rapportVeterinaire) {
            return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $rapportVeterinaire->setDate(new \DateTimeImmutable($data['date']));
        $rapportVeterinaire->setDetail($data['detail']);
        $rapportVeterinaire->setEtatAnimal($data['etat_animal']);
        $rapportVeterinaire->setNourriture($data['nourriture']);
        $rapportVeterinaire->setNourritureGrammage($data['nourriture_grammage']);

        // If updating the associated animal, race, or habitat, make sure to handle that accordingly.
        // For example, you might want to update these associations based on new data.
        if (isset($data['animal']['id'])) {
            $animalId = $data['animal']['id'];
            $animal = $this->animalRepository->find($animalId);
            if ($animal) {
                $rapportVeterinaire->setAnimal($animal);
            }
        }

        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Delete(
     *     path="/api/rapportVeterinaire/{id}",
     *     summary="Supprimer un rapport vétérinaire par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID du rapport vétérinaire"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Rapport vétérinaire supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rapport vétérinaire non trouvé"
     *     )
     * )
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $rapportVeterinaire = $this->raportVeterinaireRepository->findOneBy(['id' => $id]);
        if (!$rapportVeterinaire) {
            return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
        }

        $this->manager->remove($rapportVeterinaire);
        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }
}
