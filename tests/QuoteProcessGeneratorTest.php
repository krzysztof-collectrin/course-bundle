<?php

declare(strict_types=1);

use CourseBundle\Recommendation\Application\Service\QuoteProcessGenerator;
use CourseBundle\Recommendation\Domain\Service\QuoteCalculator;
use PHPUnit\Framework\TestCase;

class QuoteProcessGeneratorTest extends TestCase
{
    public function testTwoTopicMatchCalculation(): void
    {
        $service = new QuoteProcessGenerator(new QuoteCalculator());

        $teacherRequest = ['math' => 50, 'science' => 30, 'reading' => 20];
        $providerConfig = ['provider_topics' => [
            'provider_a' => 'math+science',
            'provider_b' => 'reading+science',
            'provider_c' => 'history+math',
        ]];

        $result = $service->generateQuotes($teacherRequest, $providerConfig);

        $this->assertEquals(8, $result['provider_a']);
    }

    public function testSingleTopicMatchCalculation(): void
    {
        $service = new QuoteProcessGenerator(new QuoteCalculator());

        $teacherRequest = ['math' => 50, 'science' => 30, 'reading' => 20];
        $providerConfig = ['provider_topics' => [
            'provider_a' => 'math+science',
            'provider_b' => 'reading+science',
            'provider_c' => 'history+math',
        ]];

        $result = $service->generateQuotes($teacherRequest, $providerConfig);

        $this->assertEquals(5, $result['provider_b']);
    }
}