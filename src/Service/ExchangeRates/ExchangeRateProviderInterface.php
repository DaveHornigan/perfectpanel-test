<?php

namespace App\Service\ExchangeRates;

use App\Service\ExchangeRates\Enum\ExchangeMethod;

interface ExchangeRateProviderInterface
{
    /** @param list<string> $currency */
    public function getRates(ExchangeMethod $method, array $currency = []): Rates;
}