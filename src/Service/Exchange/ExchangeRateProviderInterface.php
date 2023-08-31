<?php

namespace App\Service\Exchange;

use App\Service\Exchange\Enum\ExchangeMethod;

interface ExchangeRateProviderInterface
{
    /** @param list<string> $currency */
    public function getRates(array $currency = []): Rates;
}