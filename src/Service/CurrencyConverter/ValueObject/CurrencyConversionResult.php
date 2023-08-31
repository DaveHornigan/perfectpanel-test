<?php

namespace App\Service\CurrencyConverter\ValueObject;

class CurrencyConversionResult
{
    public function __construct(
        public readonly string $currencyFrom,
        public readonly string $currencyTo,
        public readonly string $valueFrom,
        public readonly string $valueTo,
        public readonly string $rate,
    ) {}
}