<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1', methods: [Request::METHOD_GET])]
class ExchangeController
{
    public function __invoke(Request $request): JsonResponse
    {
        return new JsonResponse([]);
    }
}