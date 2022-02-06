<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class ApiController extends AbstractController
{
    #[Route(path: '/api/hello', methods: ['GET'])]
    public function sayHello(): JsonResponse
    {
        return $this->json(['message' => 'Hello!']);
    }
}
