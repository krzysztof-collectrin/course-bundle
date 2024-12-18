<?php

declare(strict_types=1);

namespace CourseBundle\Recommendation\Domain\Service;

use CourseBundle\Recommendation\Domain\Entity\TeacherRequest;
use CourseBundle\Recommendation\Domain\ValueObject\ProviderTopics;

class QuoteCalculator
{
    public function calculateQuote(
        TeacherRequest $request,
        ProviderTopics $providerTopics,
    ): ?float {
        $requestedTopics = $request->getTopics();
        $matches = array_intersect(array_keys($requestedTopics), $providerTopics->getTopics());

        if (count($matches) === 2) {
            // 10% for 2 matching topics
            $sum = array_sum(array_intersect_key($requestedTopics, array_flip($matches)));

            return round(0.10 * $sum, 2);
        }

        if (count($matches) === 1) {
            // different percentages for single topic based on importance
            $matchTopic = current($matches);
            $rank = array_search($matchTopic, array_keys($requestedTopics));

            return match ($rank) {
                0 => round(0.20 * $requestedTopics[$matchTopic], 2),
                1 => round(0.25 * $requestedTopics[$matchTopic], 2),
                2 => round(0.30 * $requestedTopics[$matchTopic], 2),
                default => null,
            };
        }

        return null;
    }
}
