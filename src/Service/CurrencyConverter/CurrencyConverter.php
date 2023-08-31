<?php

namespace App\Service\CurrencyConverter;

use App\Service\CurrencyConverter\ValueObject\CurrencyConversionResult;
use App\Service\Exchange\ExchangeRateProviderInterface;
use App\Service\Exchange\Rates;

class CurrencyConverter
{
    // When adding one or more providers This service must be created using a factory with a provider selection strategy.
    public function __construct(private readonly ExchangeRateProviderInterface $rateProvider) {}

    public function convert(string $from, string $to, string $value): CurrencyConversionResult
    {
        $rates = $this->rateProvider->getRates([$from, $to]);
        $valueFrom = bccomp($value, '0.01', 10) === -1 ? '0.01' : $value;

        return match ($from) {
            'BTC' => $this->sellBTC($to, $valueFrom, $rates),
            default => $this->buyBTC($from, $valueFrom, $rates)
        };
    }

    private function buyBTC(string $from, string $value, Rates $rates): CurrencyConversionResult
    {
        if ($rates->offsetExists($from) === false) {
            throw new \Exception("Conversion from $from is not supported", 400);
        }
        $rate = $rates->offsetGet($from)->buy;
        $valueFrom = bcdiv($value, 1, 2);
        $valueTo = bcdiv($value, $rate, 10);

        return new CurrencyConversionResult($from, 'BTC', $valueFrom, $valueTo, $rate);
    }

    private function sellBTC(string $to, string $value, Rates $rates): CurrencyConversionResult
    {
        if ($rates->offsetExists($to) === false) {
            throw new \Exception("Conversion to $to is not supported", 400);
        }
        $rate = $rates->offsetGet($to)->sell;
        $valueFrom = bcdiv($value, 1, 10);
        $valueTo = bcmul($value, $rate, 2);

        return new CurrencyConversionResult('BTC', $to, $valueFrom, $valueTo, $rate);
    }
}