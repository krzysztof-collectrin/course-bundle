<?php

declare(strict_types=1);

namespace CourseBundle\Recommendation\Application\Service;

use CourseBundle\Recommendation\Domain\Entity\TeacherRequest;
use CourseBundle\Recommendation\Domain\Service\QuoteCalculator;
use CourseBundle\Recommendation\Domain\ValueObject\ProviderTopics;

readonly class QuoteProcessGenerator
{
    public function __construct(
        private QuoteCalculator $calculator,
    ) {
    }

    public function generateQuotes(
        array $teacherRequestData,
        array $providerConfig,
    ): array {
        $teacherRequest = new TeacherRequest($teacherRequestData);
        $quotes = [];

        return $this->buildQuotes(
            $providerConfig['provider_topics'],
            $teacherRequest,
            $quotes,
        );
    }

    private function buildQuotes(
        array $providerTopicsConfig,
        TeacherRequest $teacherRequest,
        array $quotes,
    ): array {
        foreach ($providerTopicsConfig as $provider => $topicsString) {
            $providerTopics = new ProviderTopics($provider, $topicsString);
            $quote = $this->calculator->calculateQuote($teacherRequest, $providerTopics);

            if ($quote > 0) {
                $quotes[$provider] = $quote;
            }
        }
        return $quotes;
    }
}
