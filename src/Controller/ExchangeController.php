<?php

namespace App\Controller;

use App\Http\DTO\Response\SuccessfullyResponse;
use App\Service\Exchange\Enum\ExchangeMethod;
use App\Service\Exchange\ExchangeRateProviderInterface;
use App\Service\Exchange\ValueObject\CurrencyRate;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
            throw new \Exception(
                'Invalid parameter "method". Expected one of: '
                . implode(', ', $expectedMethods), Response::HTTP_BAD_REQUEST
            );
        }

        return call_user_func([$this, $method], $request);
    }

    protected function rates(Request $request): JsonResponse
    {
        $currency = str_replace(['\s', '\t'], '', (string)$request->query->get('currency'));
        $currency = array_filter(explode(',', strtoupper($currency)));

        $rates = $this->provider->getRates($currency);
        $rates->uasort(fn(CurrencyRate $value1, CurrencyRate $value2) => $value1->buy <=> $value2->buy);

        return new JsonResponse(new SuccessfullyResponse($rates));
    }

    protected function convert(Request $request): JsonResponse
    {
        return new JsonResponse($this->provider->getRates());
    }
}