<?php

namespace App\Http\DTO\Response;

use Symfony\Component\HttpFoundation\Response;

class ErrorResponse
{
    public function __construct(
        public readonly string $message,
        public readonly int $code = Response::HTTP_BAD_REQUEST,
        public readonly string $status = 'error',
    ) {}
}