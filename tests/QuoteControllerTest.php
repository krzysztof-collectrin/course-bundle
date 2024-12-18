<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class QuoteControllerTest extends WebTestCase
{
    public function testValidPayload(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/quotes', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'topics' => [
                'math' => 50,
                'science' => 30,
                'reading' => 20,
            ]
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
        $this->assertStringContainsString('provider_a', $client->getResponse()->getContent());
    }

    public function testInvalidPayload(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/quotes', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'topics' => [],
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testInvalidStringTopicsType(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/quotes', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'topics' => [
                'math' => 'test',
                'science' => 'sut',
                'reading' => 20,
            ]
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testInvalidSignedIntegerTopicsType(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/quotes', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'topics' => [
                'math' => -5,
                'science' => 0,
                'reading' => -20,
            ]
        ]));

        $this->assertResponseStatusCodeSame(422);
    }
}
