<?php

declare(strict_types=1);

namespace CourseBundle\Shared\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\MissingTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

readonly class UserDataFromJWTListener
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private JWTTokenManagerInterface $jwtManager,
    ) {
    }

    public function onKernelRequest($event): void
    {
        /** @var Request $request */
        $request = $event->getRequest();

        if ('json' !== $request->getContentTypeFormat() || \str_starts_with($request->getRequestUri(), '/api/login')) {
            return;
        }

        $token = $this->tokenStorage->getToken();

        if (!$token) {
            throw new MissingTokenException();
        }

        try {
            $decodedJwtToken = $this->jwtManager->decode($token);
        } catch (JWTDecodeFailureException $exception) {
            throw new BadRequestException($exception->getMessage());
        }

        $userId = $decodedJwtToken['uid'] ?? null;

        if (null === $userId) {
            return;
        }

        $requestData = \json_decode($request->getContent(), true);
        $requestData['userId'] = $userId;

        $request->initialize(
            $request->query->all(),
            $request->request->all(),
            $request->attributes->all(),
            $request->cookies->all(),
            $request->files->all(),
            $request->server->all(),
            json_encode($requestData),
        );
    }
}
