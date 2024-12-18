<?php

declare(strict_types=1);

namespace CourseBundle\Shared\Security;

use CourseBundle\User\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $payload = $event->getData();

        /** @var User $user */
        $user = $event->getUser();

        $payload['uid'] = $user->getId();

        $event->setData($payload);
    }
}
