<?php

namespace App\Controller;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use OpenApi\Annotations as OA;




#[Route('api/role', name:'app_api_role_')]
class RoleController extends AbstractController

{
    public function __construct(
        private EntityManagerInterface $manager, 
        private RoleRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        )
    {
    }

     /**
     * @OA\Post(
     *     path="/api/role",
     *     summary="Ajouter un role",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Ajouter un role",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="label", type="string", example="Nom du rol"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Role enregistré avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="label", type="string", example="Nom du rol"),
     *         )
     *     )
     * )
     */

  #[Route(methods: 'POST')]
  public function new(Request $request): JsonResponse
  {
      $role = $this->serializer->deserialize(
          $request->getContent(),
          Role::class,
          'json'
      );
     

      // Tell Doctrine you want to (eventually) save the role (no queries yet) 
      $this->manager->persist($role);
      // Actually executes the queries (i.e. the INSERT query)
      $this->manager->flush();

      $responseData = $this->serializer->serialize($role, 'json');
      $location = $this->urlGenerator->generate(
          'app_api_role_show',
          ['id' => $role->getId()],
          UrlGeneratorInterface::ABSOLUTE_URL
      );
      return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
  }

    /**
     * @OA\Get(
     *     path="/api/role/{id}",
     *     summary="Get role by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the role"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="label", type="string", example="Nom du rol"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role not found"
     *     )
     * )
     */



   #[Route('/{id}', name: 'show', methods: 'GET')]
   public function show(int $id): JsonResponse
   {
       $role = $this->repository->findOneBy(['id' => $id]);

       if (!$role) {
           return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
       }

       $responseData = $this->serializer->serialize($role, 'json');
       return new JsonResponse(data: $responseData, status: Response::HTTP_OK, json: true);
   }

    /**
     * @OA\Put(
     *     path="/api/role/{id}",
     *     summary="Update role by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the role"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="label", type="string", example="Nom du rol"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Role updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role not found"
     *     )
     * )
     */


   #[Route('/{id}', name: 'edit', methods: 'PUT')]
   public function edit(int $id): JsonResponse
   {
       $role = $this->repository->findOneBy(['id' => $id]);
       if (!$role) {
           $this->manager->flush();
           return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
       }

       return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
   }

    /**
     * @OA\Delete(
     *     path="/api/role/{id}",
     *     summary="Delete role by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the role"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Role deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role not found"
     *     )
     * )
     */



   #[Route('/{id}', name: 'delete', methods: 'DELETE')]
   public function delete(int $id): JsonResponse
   {
       $role = $this->repository->findOneBy(['id' => $id]);
       if (!$role) {
           return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
       }

       $this->manager->remove($role);
       $this->manager->flush();

       return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
   }
}