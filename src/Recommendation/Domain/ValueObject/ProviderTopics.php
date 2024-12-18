<?php

declare(strict_types=1);

namespace CourseBundle\Recommendation\Domain\ValueObject;

class ProviderTopics
{
    private array $topics;

    public function __construct(
        private readonly string $provider,
        private string $topicsString,
    ) {
        $this->topics = explode('+', $topicsString);
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getTopics(): array
    {
        return $this->topics;
    }
}
