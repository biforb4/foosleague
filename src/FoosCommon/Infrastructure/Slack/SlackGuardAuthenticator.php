<?php

declare(strict_types=1);

namespace FoosCommon\Infrastructure\Slack;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class SlackGuardAuthenticator extends AbstractGuardAuthenticator
{
    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
    }

    public function supports(Request $request): bool
    {
        return $request->headers->has(SlackCredentials::REQUEST_SIGNATURE)
            && $request->headers->has(SlackCredentials::REQUEST_TIMESTAMP);
    }

    public function getCredentials(Request $request)
    {
        return SlackCredentials::fromRequest($request);
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        return $userProvider->loadUserByUsername(SlackCredentials::USERNAME);
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        if (!$credentials instanceof SlackCredentials) {
            return false;
        }

        return $credentials->areValidFor($user);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): ?Response
    {
        return null;
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }
}
