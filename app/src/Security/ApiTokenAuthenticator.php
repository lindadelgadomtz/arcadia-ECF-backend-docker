<?php

namespace App\Security;

use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
    private UserRepository $repository;
    private LoggerInterface $logger;

    public function __construct(UserRepository $repository, LoggerInterface $logger)
    {
        $this->repository = $repository;
        $this->logger = $logger;
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization');
    }

    public function authenticate(Request $request): Passport
    {
        $authorizationHeader = $request->headers->get('Authorization');
        $apiToken = str_replace('Bearer ', '', $authorizationHeader);

        if (!$apiToken) {
            $this->logger->warning('No API token provided.');
            throw new CustomUserMessageAuthenticationException('No API token provided.');
        }

        $user = $this->repository->findOneBy(['apiToken' => $apiToken]);
        if (!$user) {
            $this->logger->warning('Invalid API token.');
            throw new UserNotFoundException();
        }

        return new SelfValidatingPassport(new UserBadge($user->getUserIdentifier()));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $this->logger->info('Authentication successful.');
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $this->logger->error('Authentication failed: ' . $exception->getMessage());
        return new JsonResponse(
            ['error' => 'Authentication failed', 'message' => $exception->getMessage()],
            Response::HTTP_UNAUTHORIZED
        );
    }
}
