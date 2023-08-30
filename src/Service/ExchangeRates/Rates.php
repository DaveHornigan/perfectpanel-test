<?php

namespace App\Service\ExchangeRates;

use ArrayObject;

class Rates extends ArrayObject
{
    /** @param array<string, float> $rates */
    public function __construct(private readonly array $rates) {
        parent::__construct($this->rates);
    }

    /** @return array<string, float> */
    public function getSortedList(array $currencyFilter = []): array
    {
        $filteredRates = $currencyFilter !== [] ? array_filter(
            $this->rates,
            static fn(string $key) => in_array($key, $currencyFilter, true),
            ARRAY_FILTER_USE_KEY
        ) : $this->rates;
        asort($filteredRates);

        return $filteredRates;
    }
}