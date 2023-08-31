<?php

namespace App\Service\Exchange\ValueObject;

class CurrencyRate
{
    public function __construct(
        public readonly string $sell,
        public readonly string $buy,
        public readonly string $code,
    ) {}
}