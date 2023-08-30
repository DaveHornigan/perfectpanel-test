<?php

namespace App\Http\DTO\Response;

use Symfony\Component\Serializer\Annotation\SerializedName;

class CurrencyConvertResponseData
{
    public function __construct(
        #[SerializedName('currency_from')]
        public readonly string $currencyFrom,
        #[SerializedName('currency_to')]
        public readonly string $currencyTo,
        public readonly string $value,
        #[SerializedName('converted_value')]
        public readonly string $convertedValue,
        public readonly string $rate
    ) {}
}