<?php

namespace App\Controller;

use App\Service\ExchangeRates\Enum\ExchangeMethod;
use App\Service\ExchangeRates\ExchangeRateProviderInterface;
use App\Service\ExchangeRates\Provider\AbstractExchangeRateProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1', methods: [Request::METHOD_GET])]
final class ExchangeController
{
    public function __construct(private readonly ExchangeRateProviderInterface $provider) {}

    /**
     * a crutch due to the lack of a mechanism for choosing a controller method by a query parameter...
     * extending the router for this, in the context of the test task, will be superfluous
     * @uses rates
     * @uses convert
     */
    public function __invoke(Request $request): JsonResponse
    {
        $expectedMethods = ['rates', 'convert'];
        $method = strtolower((string)$request->query->get('method'));
        if (false === in_array($method, $expectedMethods, true)) {
            throw new \Exception('Invalid parameter "method". Expected one of: ' . implode(', ', $expectedMethods));
        }

        return call_user_func([$this, $method], $request);
    }

    protected function rates(Request $request): JsonResponse
    {
        $currency = str_replace(['\s', '\t'], '', (string)$request->query->get('currency'));
        $currency = explode(',', strtoupper($currency));

        return new JsonResponse($this->provider->getRates(ExchangeMethod::Sell, $currency));
    }

    protected function convert(Request $request): JsonResponse
    {
        return new JsonResponse($this->provider->getRates(ExchangeMethod::Sell));
    }
}