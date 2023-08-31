<?php

namespace App\Http\DTO\Response;

use Symfony\Component\Serializer\Annotation\SerializedName;

class CurrencyConvertResponseData implements \JsonSerializable
{
    public function __construct(
        #[SerializedName('currency_from')]
        public readonly string $currencyFrom,
        #[SerializedName('currency_to')]
        public readonly string $currencyTo,
        #[SerializedName('value')]
        public readonly string $valueFrom,
        #[SerializedName('converted_value')]
        public readonly string $valueTo,
        public readonly string $rate,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'currency_from' => $this->currencyFrom,
            'currency_to' => $this->currencyTo,
            'value' => $this->valueFrom,
            'converted_value' => $this->valueTo,
            'rate' => $this->rate,
        ];
    }
}