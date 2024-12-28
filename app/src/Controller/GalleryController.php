<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Habitat;
use App\Entity\Animal;
use App\Repository\GalleryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('api/gallery', name: 'app_api_gallery_')]
class GalleryController extends AbstractController
{
    private EntityManagerInterface $manager;
    private GalleryRepository $repository;
    private SerializerInterface $serializer;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        EntityManagerInterface $manager,
        GalleryRepository $repository,
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator,
    ) {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->serializer = $serializer;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @OA\Post(
     *     path="/api/gallery",
     *     summary="Add a picture",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Add a picture",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="title", type="string", example="Sample Image"),
     *             @OA\Property(property="image_data", type="string", format="binary"),
     *             @OA\Property(property="habitat", type="integer", example=1),
     *             @OA\Property(property="animal", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Image uploaded successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="Sample Image"),
     *             @OA\Property(property="url_image", type="string", example="/img/sample-image-unique-id.jpg"),
     *             @OA\Property(property="habitat", type="integer", example=1),
     *             @OA\Property(property="animal", type="array", @OA\Items(type="integer"))
     *         )
     *     )
     * )
     */
    #[Route(methods: ['POST'])]
    public function uploadImage(Request $request): JsonResponse
    {
        $file = $request->files->get('image');
        $title = $request->request->get('title');
        $habitatId = $request->request->get('habitat');
        $animalIds = $request->request->get('animal');
        $gallery = new Gallery();

        if (!$file || !$title) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        if ($animalIds) {
            // Decode animalIds if it is a JSON string
            if (is_string($animalIds)) {
                $animalIds = json_decode($animalIds, true);
            }

            if (!is_array($animalIds)) {
                return new JsonResponse(['error' => 'Animal IDs should be an array'], Response::HTTP_BAD_REQUEST);
            }

            $animalEntities = [];
            foreach ($animalIds as $animalId) {
                $animal = $this->manager->getRepository(Animal::class)->find($animalId);
                if (!$animal) {
                    return new JsonResponse(['error' => 'Animal with ID ' . $animalId . ' not found'], Response::HTTP_NOT_FOUND);
                }
                $animalEntities[] = $animal;
            }
            foreach ($animalEntities as $animal) {
                $gallery->setAnimal($animal);
            }
        }

        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/img';
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = $filename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($uploadDir, $newFilename);
        } catch (FileException $e) {
            return new JsonResponse(['error' => 'Failed to upload image'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $habitat = $this->manager->getRepository(Habitat::class)->find($habitatId ? $habitatId : 12);
        if (!$habitat) {
            return new JsonResponse(['error' => 'Habitat not found'], Response::HTTP_NOT_FOUND);
        }
        $gallery->setHabitat($habitat);


        $gallery->setTitle($title);
        $gallery->setUrlImage('/img/' . $newFilename);




        $this->manager->persist($gallery);
        $this->manager->flush();

        return new JsonResponse([
            'message' => 'Image uploaded successfully',
            'id' => $gallery->getId(),
            'title' => $gallery->getTitle(),
            'url_image' => $gallery->getUrlImage(),
            'habitat' => $gallery->getHabitat() ? $gallery->getHabitat()->getId() : null,
            'animals' => array_map(fn ($animal) => $animal->getId(), $gallery->getAnimals()->toArray()),
        ], Response::HTTP_CREATED);
    }

    // /**
    //  * @OA\Put(
    //  *     path="/api/gallery/{id}",
    //  *     summary="Update gallery by ID",
    //  *     @OA\Parameter(
    //  *         name="id",
    //  *         in="path",
    //  *         required=true,
    //  *         @OA\Schema(type="integer"),
    //  *         description="ID of the gallery"
    //  *     ),
    //  *     @OA\RequestBody(
    //  *         required=true,
    //  *         @OA\JsonContent(
    //  *             type="object",
    //  *             @OA\Property(property="id", type="integer", example=1),
    //  *             @OA\Property(property="title", type="string", example="Sample Image"),
    //  *             @OA\Property(property="url_image", type="string", example="/img/sample-image-unique-id.jpg"),
    //  *             @OA\Property(property="service", type="integer", example=1)
    //  *         )
    //  *     ),
    //  *     @OA\Response(
    //  *         response=204,
    //  *         description="Picture updated successfully"
    //  *     ),
    //  *     @OA\Response(
    //  *         response=404,
    //  *         description="Picture not found"
    //  *     )
    //  * )
    //  */

     #[Route('/{id}', name: 'edit', methods: ['PUT'])] 
     public function edit(int $id, Request $request): JsonResponse
     {
         $gallery = $this->repository->findOneBy(['id' => $id]);
         if (!$gallery) {
             return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
         }
 
         $data = json_decode($request->getContent(), true);
         $gallery->setTitle($data['title'] ?? $gallery->getTitle());
         $gallery->setUrlImage($data['url_image'] ?? $gallery->getUrlImage());
         $gallery->setService($data['service.id'] ?? $gallery->getService());
 
         $this->manager->flush();

         return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
     }

    /**
     * @OA\Get(
     *     path="/api/gallery/{id}",
     *     summary="Get image by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the image"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Image details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="Sample Image"),
     *             @OA\Property(property="url_image", type="string", example="/img/sample-image-unique-id.jpg"),
     *             @OA\Property(property="habitat", type="integer", example=1),
     *             @OA\Property(property="animals", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Image not found"
     *     )
     * )
     */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $gallery = $this->repository->find($id);

        if (!$gallery) {
            return new JsonResponse(['error' => 'Image not found'], Response::HTTP_NOT_FOUND);
        }

        $responseData = $this->serializer->serialize($gallery, 'json', [
            AbstractNormalizer::GROUPS => ['gallery:read']
        ]);

        // Decode the serialized data to add habitat_id and animals
        $responseArray = json_decode($responseData, true);

        $responseArray['habitat'] = $gallery->getHabitat() ? $gallery->getHabitat()->getId() : null;
        $responseArray['animals'] = array_map(fn ($animal) => $animal->getId(), $gallery->getAnimals()->toArray());

        return new JsonResponse($responseArray, Response::HTTP_OK);
    }

     /**
     * @OA\Delete(
     *     path="/api/gallery/{id}",
     *     summary="Delete Picture by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the Picture"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Picture deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Picture not found"
     *     )
     * )
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $gallery = $this->repository->findOneBy(['id' => $id]);
        if (!$gallery) {
            return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
        }

        $this->manager->remove($gallery);
        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }
}