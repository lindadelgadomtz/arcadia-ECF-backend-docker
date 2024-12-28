<?php

/*
namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;



#[Route('api/utilisateur', name:'app_api_utilisateur_')]
class UtilisateurController extends AbstractController

{
    public function __construct(
        private EntityManagerInterface $manager, 
        private UtilisateurRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        )
    {
    }
  #[Route(methods: 'POST')]
  public function new(Request $request): JsonResponse
  {
      $utilisateur = $this->serializer->deserialize(
          $request->getContent(),
          Utilisateur::class,
          'json'
      );
  

      // Tell Doctrine you want to (eventually) save the utilisateur (no queries yet) 
      $this->manager->persist($utilisateur);
      // Actually executes the queries (i.e. the INSERT query)
      $this->manager->flush();

      $responseData = $this->serializer->serialize($utilisateur, 'json');
      $location = $this->urlGenerator->generate(
          'app_api_utilisateur_show',
          ['id' => $utilisateur->getId()],
          UrlGeneratorInterface::ABSOLUTE_URL
      );
      return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
  }


   #[Route('/{id}', name: 'show', methods: 'GET')]
   public function show(int $id): JsonResponse
   {
       $utilisateur = $this->repository->findOneBy(['id' => $id]);

       if (!$utilisateur) {
           return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
       }

       $responseData = $this->serializer->serialize($utilisateur, 'json');
       return new JsonResponse(data: $responseData, status: Response::HTTP_OK, json: true);
   }

   #[Route('/{id}', name: 'edit', methods: 'PUT')]
   public function edit(int $id): JsonResponse
   {
       $utilisateur = $this->repository->findOneBy(['id' => $id]);
       if (!$utilisateur) {
           $this->manager->flush();
           return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
       }

       return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
   }

   #[Route('/{id}', name: 'delete', methods: 'DELETE')]
   public function delete(int $id): JsonResponse
   {
       $utilisateur = $this->repository->findOneBy(['id' => $id]);
       if (!$utilisateur) {
           return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
       }

       $this->manager->remove($utilisateur);
       $this->manager->flush();

       return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
   }
}
*/
?>