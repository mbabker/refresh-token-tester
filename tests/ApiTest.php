<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ApiTest extends WebTestCase
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

        $this->assertArrayHasKey('token', $data, 'JWT is not in the response');
        $this->assertArrayHasKey('refresh_token', $data, 'Refresh token is not in the response');

        $jwt = $data['token'];
        $refreshToken = $data['refresh_token'];

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $jwt));

        $client->jsonRequest(
            method: 'GET',
            uri: '/api/hello',
        );

        $this->assertResponseIsSuccessful('Could not say hello.');

        $response = $client->getResponse();

        if (false === $response->getContent()) {
            $this->fail('The API did not send a proper response.');
        }

        $data = json_decode(json: $response->getContent(), associative: true, flags: \JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('message', $data, 'Message is not in the response');

        $client->jsonRequest(
            method: 'POST',
            uri: '/api/token/refresh',
            parameters: ['refresh_token' => $refreshToken],
        );

        $this->assertResponseIsSuccessful('Could not refresh the API token.');

        $response = $client->getResponse();

        if (false === $response->getContent()) {
            $this->fail('The API did not send a proper response.');
        }

        $data = json_decode(json: $response->getContent(), associative: true, flags: \JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('token', $data, 'JWT is not in the response');
        $this->assertArrayHasKey('refresh_token', $data, 'Refresh token is not in the response');
    }
}
