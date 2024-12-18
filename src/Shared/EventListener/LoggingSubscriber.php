<?php

declare(strict_types=1);

namespace CourseBundle\Shared\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

readonly class LoggingSubscriber
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $this->logger->info('Incoming request', [
            'method' => $request->getMethod(),
            'path' => $request->getPathInfo(),
            'content' => $request->getContent(),
        ]);
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $this->logger->info('Outgoing response', [
            'status_code' => $response->getStatusCode(),
            'content' => $response->getContent(),
        ]);
    }
}
