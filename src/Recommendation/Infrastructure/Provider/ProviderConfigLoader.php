<?php

declare(strict_types=1);

namespace CourseBundle\Recommendation\Infrastructure\Provider;

readonly class ProviderConfigLoader
{
    public function __construct(private string $filePath)
    {
    }

    public function load(): array
    {
        // static config file could be stored on AWS S3 and fetched by AWS SDK client
        $jsonContent = file_get_contents($this->filePath);
        return \json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);
    }
}
