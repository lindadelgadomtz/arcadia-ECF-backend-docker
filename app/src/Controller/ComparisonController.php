<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\RapportVeterinaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\AnimalFeeding;

class ComparisonController extends AbstractController
{
   // DOCUMENTATION
    // /**
    //  * @OA\Get(
    //  *     path="/compareFoodLog/{animalId}/{date}",
    //  *     summary="Comparer un rapport vétérinaire et rapport employee par ID",
    //  *     @OA\Parameter(
    //  *         name="id",
    //  *         in="path",
    //  *         required=true,
    //  *         @OA\Schema(type="integer"),
    //  *         description="ID du animal"
    //  *     ),
    //  *     @OA\Response(
    //  *         response=200,
    //  *         description="Détails du rapport vétérinaire",
    //  *         @OA\JsonContent(
    //  *             type="object",
    //  *             @OA\Property(property="id", type="integer", example=1),
    //  *             @OA\Property(property="date", type="string", format="date", example="2024-07-18"),
    //  *             @OA\Property(property="detail", type="string", example="Détails du rapport"),
    //  *             @OA\Property(property="animal", type="object", example={"id": 1}),
    //  *             @OA\Property(property="etat_animal", type="string", example="État de l'animal"),
    //  *             @OA\Property(property="nourriture", type="string", example="Type de nourriture"),
    //  *             @OA\Property(property="nourriture_grammage", type="integer", example=500)
    //  *         )
    //  *     ),
    //  *     @OA\Response(
    //  *         response=404,
    //  *         description="Rapport vétérinaire non trouvé"
    //  *     )
    //  * )
    //  */
    
    #[Route('api/compareFoodLog/{animalId}/{date}', name: 'compare_food_log', methods: ['GET'])]
    public function compareFoodLog($animalId, $date, EntityManagerInterface $entityManager): JsonResponse
    {
        $animal = $entityManager->getRepository(Animal::class)->find($animalId);
        $rawAnimalFeedings = $animal->getAnimalFeedings()->toArray();;

        $animalFeedings = array_filter($rawAnimalFeedings, function (AnimalFeeding $feeding) use ($date) {
            return $feeding->getDate()->format('Y-m-d') == $date;
        });
    
        if (!$animal) {
            return new JsonResponse(['error' => 'Animal not found'], 404);
        }
    
        // Fetch the "Rapport Veterinaire" for the selected date
        $veterinaryReports = $entityManager->getRepository(RapportVeterinaire::class)
            ->findBy(['animal' => $animal, 'Date' => new \DateTimeImmutable($date)]);

        // Structure the data to return as JSON
        $data = [
            'animal' => $animal->getPrenom(),
            'veterinaryReports' => array_map(function ($report) {
                return [
                    'etat_animal' => $report->getEtatAnimal(),
                    'nourriture' => $report->getNourriture(),
                    'nourriture_grammage' => $report->getNourritureGrammage(),
                    'detail' => $report->getDetail(),
                ];
            }, $veterinaryReports),
            'animalFeedings' => array_map(function ($feeding) {
                return [
                    'id' => $feeding->getId(),
                    'date' => $feeding->getDate()->format('Y-m-d'),
                    'nourriture' => $feeding->getNourriture(),
                    'nourriture_grammage_emp' => $feeding->getNourritureGrammageEmp(),
                ];
            }, $animalFeedings),
        ];
    
        return new JsonResponse($data);
    }
}