<?php

namespace App\Controller;

use App\Http\DTO\Request\CurrencyConvertRequest;
use App\Http\DTO\Response\CurrencyConvertResponse;
use App\Http\DTO\Response\CurrencyConvertResponseData;
use App\Http\DTO\Response\SuccessfullyResponse;
use App\Service\CurrencyConverter\CurrencyConverter;
use App\Service\CurrencyConverter\ValueObject\CurrencyConversionResult;
use App\Service\Exchange\ExchangeRateProviderInterface;
use App\Service\Exchange\ValueObject\CurrencyRate;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/v1', methods: [Request::METHOD_GET, Request::METHOD_POST])]
final class ExchangeController
{
    private const SUPPORTED_METHODS = [
        'convert' => [Request::METHOD_POST],
        'rates' => [Request::METHOD_GET],
    ];

    // It would be possible to deliver dependence directly to the method if the router supported the required format of the end points
    public function __construct(
        private readonly ExchangeRateProviderInterface $provider,
        private readonly CurrencyConverter $currencyConverter,
        private readonly SerializerInterface $serializer,
    ) {}

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
        if (false === array_key_exists($method, self::SUPPORTED_METHODS)) {
            // Here would be 404 if the choice of action was determined by url, and not by query parameter
            throw new \Exception(
                'Invalid parameter "method". Expected one of: '
                . implode(', ', $expectedMethods), Response::HTTP_BAD_REQUEST
            );
        }
        if (false === in_array($request->getMethod(), self::SUPPORTED_METHODS[$method])) {
            throw new \Exception('HTTP method not allowed', Response::HTTP_METHOD_NOT_ALLOWED);
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
        if (empty($request->getContent())) {
            throw new \Exception('Request body can\'t be empty', Response::HTTP_BAD_REQUEST);
        }
        try {
            /** @var CurrencyConvertRequest $data */
            $data = $this->serializer->deserialize($request->getContent(), CurrencyConvertRequest::class, 'json');
        } catch (\Exception $e) {
//            throw new \Exception('Invalid json format', Response::HTTP_BAD_REQUEST, $e);
            throw new \Exception($e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }

        $result = $this->currencyConverter->convert(
            $data->currencyFrom,
            $data->currencyTo,
            $data->valueFrom
        );

        $response = new CurrencyConvertResponseData(
            $result->currencyFrom,
            $result->currencyTo,
            $result->valueFrom,
            $result->valueTo,
            $result->rate,
        );

        return new JsonResponse(new SuccessfullyResponse($response->jsonSerialize()));
    }
}