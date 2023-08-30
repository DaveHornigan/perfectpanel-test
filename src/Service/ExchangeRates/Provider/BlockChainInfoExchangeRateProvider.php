<?php

namespace App\Service\ExchangeRates\Provider;

use App\Service\ExchangeRates\Enum\ExchangeMethod;
use App\Service\ExchangeRates\Rates;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class BlockChainInfoExchangeRateProvider extends AbstractExchangeRateProvider
{
    private const BLOCKCHAIN_URL = 'https://blockchain.info/ticker';

    public function __construct(
        private readonly ClientInterface $client,
        ParameterBagInterface $parameters
    ) {
        parent::__construct($parameters);
    }

    /**
     * Returns list of 1 BTC prices
     * @psalm-return array<string, array{15m: float, last: float, buy: float, sell: float, symbol: string}>
     */
    public function getRates(ExchangeMethod $method, array $currency = []): Rates
    {
        $request = new Request('GET', self::BLOCKCHAIN_URL);

        try {
            $response = $this->client->sendRequest($request);
            $allRates = json_decode($response->getBody(), true, flags: JSON_THROW_ON_ERROR);
            $filteredRates = $currency !== [] ? array_filter(
                $allRates,
                static fn(string $key) => in_array($key, $currency, true),
                ARRAY_FILTER_USE_KEY
            ) : $allRates;
            asort($filteredRates);

            $key = match ($method) {
                ExchangeMethod::Sell => 'sell',
                default => 'buy'
            };

            $rates = array_map(
                fn(array $item) => $this->getRateWithCommission($method, $item[$key]),
                $filteredRates
            );

            return new Rates($rates);
        } catch (ClientExceptionInterface $e) {
            throw new \Exception('External service unavailable', previous: $e);
        } catch (\JsonException $e) {
            throw new \Exception('Unexpected response format from external service', previous: $e);
        }
    }
}