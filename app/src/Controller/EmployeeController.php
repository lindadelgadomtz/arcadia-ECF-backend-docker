<?php

namespace App\Controller;

use App\Entity\AnimalFeeding;
use App\Repository\AvisRepository;
use App\Repository\ServiceRepository;
use App\Repository\AnimalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class EmployeeController extends AbstractController
{
    #[Route('api/employee/validate-avis/{id}', name: 'employee_validate_avis', methods: ['POST'])]
    public function validateAvis(AvisRepository $avisRepository, EntityManagerInterface $em, int $id): JsonResponse
    {
        $avis = $avisRepository->find($id);
        if (!$avis) {
            return new JsonResponse(['message' => 'Avis not found'], Response::HTTP_NOT_FOUND);
        }

        $avis->setIsVisible(true);
        $em->flush();

        return new JsonResponse(['message' => 'Avis validated']);
    }

    #[Route('api/employee/invalidate-avis/{id}', name: 'employee_invalidate_avis', methods: ['POST'])]
    public function invalidateAvis(AvisRepository $avisRepository, EntityManagerInterface $em, int $id): JsonResponse
    {
        $avis = $avisRepository->find($id);
        if (!$avis) {
            return new JsonResponse(['message' => 'Avis not found'], Response::HTTP_NOT_FOUND);
        }

        $avis->setIsVisible(false);
        $em->flush();

        return new JsonResponse(['message' => 'Avis invalidated']);
    }

    #[Route('/employee/update-service/{id}', name: 'employee_update_service', methods: ['POST'])]
    public function updateService(Request $request, ServiceRepository $serviceRepository, EntityManagerInterface $em, int $id): JsonResponse
    {
        $service = $serviceRepository->find($id);
        if (!$service) {
            return new JsonResponse(['message' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['nom'])) {
            $service->setNom($data['nom']);
        }
        if (isset($data['description'])) {
            $service->setDescription($data['description']);
        }

        $em->flush();

        return new JsonResponse(['message' => 'Service updated']);
    }

    #[Route('/employee/feed-animal', name: 'employee_feed_animal', methods: ['POST'])]
    public function feedAnimal(Request $request, AnimalRepository $animalRepository, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $animal = $animalRepository->find($data['animal_id']);
        if (!$animal) {
            return new JsonResponse(['message' => 'Animal not found'], Response::HTTP_NOT_FOUND);
        }

        $feeding = new AnimalFeeding();
        $feeding->addAnimal($animal);
        $feeding->setDate(new \DateTimeImmutable($data['date']));
        $feeding->setNourriture($data['food']);
        $feeding->setNourritureGrammageEmp ($data['quantity']);

        $em->persist($feeding);
        $em->flush();

        return new JsonResponse(['message' => 'Animal fed']);
    }
}
