<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1')]
class ExchangeController
{
    #[Route(methods: [Request::METHOD_GET])]
    public function rates(): JsonResponse
    {
        return new JsonResponse([]);
    }
}