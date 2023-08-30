<?php

namespace App\Service\ExchangeRates\Provider;

use App\Service\ExchangeRates\Enum\ExchangeMethod;
use App\Service\ExchangeRates\ExchangeRateProviderInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

abstract class AbstractExchangeRateProvider implements ExchangeRateProviderInterface
{
    private readonly int $commissionPercent;

    public function __construct(ParameterBagInterface $parameters)
    {
        $this->commissionPercent = $parameters->get('service_commission_percent');
    }

    protected function getRateWithCommission(ExchangeMethod $method, float $rate): float
    {
        $rateWithCommission = match ($method) {
            ExchangeMethod::Sell => $rate + ($rate / 100 * $this->commissionPercent),
            default => $rate - ($rate / 100 * $this->commissionPercent),
        };

        return round($rateWithCommission, 10);
    }
}