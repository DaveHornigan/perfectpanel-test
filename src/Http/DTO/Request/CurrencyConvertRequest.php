<?php

namespace App\Http\DTO\Request;

use Symfony\Component\Serializer\Annotation\SerializedName;

class CurrencyConvertRequest
{
    public function __construct(
        #[SerializedName('currency_from')]
        public readonly string $currencyFrom,
        #[SerializedName('currency_to')]
        public readonly string $currencyTo,
        #[SerializedName('value')]
        public readonly string|float|int $valueFrom
    ) {}
}