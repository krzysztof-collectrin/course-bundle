<?php

declare(strict_types=1);

namespace CourseBundle\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class QuoteControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->createAuthenticatedClient(
            'chammes@daugherty.com',
            'password123',
        );
    }

    protected function createAuthenticatedClient($username = 'user', $password = 'password'): void
    {
        $this->client->jsonRequest(
            'POST',
            '/api/login_check',
            [
                'username' => $username,
                'password' => $password,
            ]
        );

        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->client->setServerParameter('Authorization', sprintf('Bearer %s', $data['token']));
    }

    public function testValidPayload(): void
    {
        $this->client->request('POST', '/api/quotes', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'topics' => [
                'math' => 50,
                'science' => 30,
                'reading' => 20,
            ]
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertStringContainsString('provider_a', $this->client->getResponse()->getContent());
    }

    public function testInvalidPayload(): void
    {
        $this->client->request('POST', '/api/quotes', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'topics' => [],
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testInvalidStringTopicsType(): void
    {
        $this->client->request('POST', '/api/quotes', [], [], [
            'CONTENT_TYPE' => 'application/json',
            ], json_encode([
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
        $this->client->request('POST', '/api/quotes', [], [], [
            'CONTENT_TYPE' => 'application/json',
            ], json_encode([
            'topics' => [
                'math' => -5,
                'science' => 0,
                'reading' => -20,
            ]
        ]));

        $this->assertResponseStatusCodeSame(422);
    }
}
