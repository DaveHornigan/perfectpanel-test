<?php

namespace App\Http\DTO\Response;

use Symfony\Component\HttpFoundation\Response;

class SuccessfullyResponse
{
    /**
     * @template T
     * @param object<T> $data
     */
    public function __construct(
        public readonly mixed $data,
        public readonly int $code = Response::HTTP_OK,
        public readonly string $status = 'success',
    ) {}
}