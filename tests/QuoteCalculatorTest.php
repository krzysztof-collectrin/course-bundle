<?php

declare(strict_types=1);

use CourseBundle\Recommendation\Domain\Entity\TeacherRequest;
use CourseBundle\Recommendation\Domain\Service\QuoteCalculator;
use CourseBundle\Recommendation\Domain\ValueObject\ProviderTopics;
use PHPUnit\Framework\TestCase;

class QuoteCalculatorTest extends TestCase
{
    private QuoteCalculator $calculator;

    public function setUp(): void
    {
        $this->calculator = new QuoteCalculator();
    }

    public function testCalculatesQuoteForTwoTopicMatchesWithProviderA(): void
    {
        $request = $this->prepareTeacherRequest();
        $providerTopics = new ProviderTopics('provider_a', 'math+science');

        $this->assertEquals(8, $this->calculator->calculateQuote($request, $providerTopics));
    }

    public function testCalculatesQuoteForTwoTopicMatchWithProviderB(): void
    {
        $request = $this->prepareTeacherRequest();
        $providerTopics = new ProviderTopics('provider_b', 'reading+science');

        $this->assertEquals(5, $this->calculator->calculateQuote($request, $providerTopics));
    }

    public function testCalculatesQuoteForOneTopicMatchWithProviderC(): void
    {
        $request = $this->prepareTeacherRequest();
        $providerTopics = new ProviderTopics('provider_c', 'history+math');

        $this->assertEquals(10, $this->calculator->calculateQuote($request, $providerTopics));
    }

    private function prepareTeacherRequest(): TeacherRequest
    {
        return new TeacherRequest(['math' => 50, 'science' => 30, 'reading' => 20]);
    }
}
