<?php

namespace App\Controller;

use App\Entity\User;
use DateTimeImmutable;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Http\Attribute\CurrentUser;
    use Symfony\Component\Serializer\SerializerInterface;
    use OpenApi\Annotations as OA;
    use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
    use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

    #[Route('/api', name: 'app_api_')]
    class SecurityController extends AbstractController
    {
        public function __construct(
            private EntityManagerInterface $manager,
            private SerializerInterface $serializer,
            private UserPasswordHasherInterface $passwordHasher
        ) {
        }

        /**
         * @OA\Post(
         *     path="/api/registration",
         *     summary="Inscription d'un nouvel utilisateur",
         *     @OA\RequestBody(
         *         required=true,
         *         description="Données de l'utilisateur à inscrire",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="email", type="string", example="adresse@email.com"),
         *             @OA\Property(property="password", type="string", example="Mot de passe")
         *         )
         *     ),
         *     @OA\Response(
         *         response=201,
         *         description="Utilisateur inscrit avec succès",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="user", type="string", example="Nom d'utilisateur"),
         *             @OA\Property(property="apiToken", type="string", example="31a023e212f116124a36af14ea0c1c3806eb9378"),
         *             @OA\Property(property="roles", type="array", @OA\Items(type="string", example="ROLE_USER"))
         *         )
         *     )
         * )
         */
        #[Route('/registration', name: 'registration', methods: ['POST'])]
        public function register(Request $request): JsonResponse
        {
            $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
            $user->setCreatedAt(new DateTimeImmutable());
            $user->setRoles($user->getRoles());

            $this->manager->persist($user);
            $this->manager->flush();

            return new JsonResponse(
                ['user' => $user->getUserIdentifier(), 'apiToken' => $user->getApiToken(), 'roles' => $user->getRoles()],
                Response::HTTP_CREATED
            );
        }

        /**
         * @OA\Post(
         *     path="/api/login",
         *     summary="Connecter un utilisateur",
         *     @OA\RequestBody(
         *         required=true,
         *         description="Données de l’utilisateur pour se connecter",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="username", type="string", example="adresse@email.com"),
         *             @OA\Property(property="password", type="string", example="Mot de passe")
         *         )
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="Connexion réussie",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="user", type="string", example="Nom d'utilisateur"),
         *             @OA\Property(property="apiToken", type="string", example="31a023e212f116124a36af14ea0c1c3806eb9378"),
         *             @OA\Property(property="roles", type="array", @OA\Items(type="string", example="ROLE_USER"))
         *         )
         *     )
         * )
         */
        #[Route('/login', name: 'login', methods: ['POST'])]
        public function login(): JsonResponse
        {
            $user = $this->getUser();

            if (null === $user) {
                return new JsonResponse(['message' => 'Missing credentials'], Response::HTTP_UNAUTHORIZED);
            }

            return new JsonResponse([
                'id' => $user->getId(),
                'user' => $user->getUserIdentifier(),
                'apiToken' => $user->getApiToken(),
                'roles' => $user->getRoles(),
            ]);
        }

        /**
         * @OA\Get(
         *     path="/api/account/me",
         *     summary="Récupérer toutes les informations de l'objet User",
         *     @OA\Response(
         *         response=200,
         *         description="Tous les champs utilisateurs retournés",
         *     )
         * )
         */
        #[Route('/account/me', name: 'me', methods: ['GET'])]
        public function me(): JsonResponse
        {
            $user = $this->getUser();

            $responseData = $this->serializer->serialize($user, 'json');

            return new JsonResponse([
                'user' => $user->getUserIdentifier(),
                'apiToken' => $user->getApiToken(),
                'roles' => $user->getRoles(),
            ], Response::HTTP_OK, [], true);
        }

        /**
         * @OA\Put(
         *     path="/api/account/edit",
         *     summary="Modifier son compte utilisateur avec l'un ou tous les champs",
         *     @OA\RequestBody(
         *         required=true,
         *         description="Nouvelles données éventuelles de l'utilisateur à mettre à jour",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="firstName", type="string", example="Nouveau prénom"),
         *             @OA\Property(property="password", type="string", example="Nouveau mot de passe")
         *         )
         *     ),
         *     @OA\Response(
         *         response=204,
         *         description="Utilisateur modifié avec succès"
         *     )
         * )
         */
        #[Route('/account/edit', name: 'edit', methods: ['PUT'])]
        public function edit(Request $request): JsonResponse
        {
            $user = $this->getUser();

            if (!$user instanceof User) {
                return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            $this->serializer->deserialize(
                $request->getContent(),
                User::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $user]
            );

            $user->setUpdatedAt(new DateTimeImmutable());

            $data = $request->toArray();
            if (isset($data['password'])) {
                $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));
            }

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
    }
