<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ApiLoginTest extends WebTestCase
{
    public function testLoginAndRefreshApiToken(): void
    {
        $client = self::createClient();

        $client->jsonRequest(
            method: 'POST',
            uri: '/api/login',
            parameters: ['username' => 'user1', 'password' => 'password'],
        );

        $this->assertResponseIsSuccessful('Could not authenticate to the API.');

        $response = $client->getResponse();

        if (false === $response->getContent()) {
            $this->fail('The API did not send a proper response.');
        }

        $data = json_decode(json: $response->getContent(), associative: true, flags: \JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('token', $data, 'JWT token is not in the response');
        $this->assertArrayHasKey('refresh_token', $data, 'Refresh token is not in the response');

        $client->jsonRequest(
            method: 'POST',
            uri: '/api/token/refresh',
            parameters: ['refresh_token' => $data['refresh_token']],
        );

        $this->assertResponseIsSuccessful('Could not refresh the API token.');

        $response = $client->getResponse();

        if (false === $response->getContent()) {
            $this->fail('The API did not send a proper response.');
        }

        $data = json_decode(json: $response->getContent(), associative: true, flags: \JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('token', $data, 'JWT token is not in the response');
        $this->assertArrayHasKey('refresh_token', $data, 'Refresh token is not in the response');
    }
}
