<?php

declare(strict_types=1);

namespace CourseBundle\Recommendation\Application\Service;

use CourseBundle\Recommendation\Domain\Entity\TeacherRequest;
use CourseBundle\Recommendation\Domain\Service\QuoteCalculator;
use CourseBundle\Recommendation\Domain\ValueObject\ProviderTopics;
use Psr\Log\LoggerInterface;

readonly class QuoteProcessGenerator
{
    public function __construct(
        private QuoteCalculator $calculator,
        private LoggerInterface $logger,
    ) {
    }

    public function generateQuotes(
        array $teacherRequestData,
        array $providerConfig,
    ): array {
        $teacherRequestData = $this->sanitizeInputData($teacherRequestData);

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

            $this->logger->info('Received teacher request.', ['topics' => $teacherRequest->getTopics()]);

            $quote = $this->calculator->calculateQuote($teacherRequest, $providerTopics);
            if ($quote > 0) {
                $quotes[$provider] = $quote;
            }
        }
        $this->logger->info('Quotes generated successfully.', ['quotes' => $quotes]);

        return $quotes;
    }

    private function sanitizeInputData(array $teacherRequestData): array
    {
        foreach ($teacherRequestData as $key => $value) {
            $sanitizedKey = \htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
            $sanitizedValue = \filter_var($value, FILTER_SANITIZE_NUMBER_INT);

            $teacherRequestData[$sanitizedKey] = $sanitizedValue;
        }

        return $teacherRequestData;
    }
}
