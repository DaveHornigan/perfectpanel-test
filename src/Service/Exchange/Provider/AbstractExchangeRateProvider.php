<?php

namespace App\Service\Exchange\Provider;

use App\Service\Exchange\ExchangeRateProviderInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

abstract class AbstractExchangeRateProvider implements ExchangeRateProviderInterface
{
    private readonly int $commissionPercent;

    public function __construct(ParameterBagInterface $parameters)
    {
        $this->commissionPercent = $parameters->get('service_commission_percent');
    }

    private function getCommission(float $value): float
    {
        return $value / 100 * $this->commissionPercent;
    }

    protected function getSellRateWithCommission(float $originalRate): string
    {
        $rateWithCommission = $originalRate + $this->getCommission($originalRate);

        return bcdiv($rateWithCommission, 1, 2);
    }

    protected function getBuyRateWithCommission(float $originalRate): string
    {
        $rateWithCommission = $originalRate - $this->getCommission($originalRate);

        return bcdiv($rateWithCommission, 1, 2);
    }
}