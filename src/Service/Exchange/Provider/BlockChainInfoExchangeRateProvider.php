<?php

namespace App\Service\Exchange\Provider;

use App\Service\Exchange\Rates;
use App\Service\Exchange\ValueObject\CurrencyRate;
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
     * Returns array object with list of 1 BTC prices
     */
    public function getRates(array $currency = []): Rates
    {
        $request = new Request('GET', self::BLOCKCHAIN_URL);

        try {
            // Here it makes sense to cache the result and return it if it is impossible to get data from the service for greater fault tolerance.
            // But when exchanging, it can cause losses
            $response = $this->client->sendRequest($request);
            $allRates = json_decode($response->getBody(), true, flags: JSON_THROW_ON_ERROR);
            $filteredRates = $currency !== [] ? array_filter(
                $allRates,
                static fn(string $key) => in_array($key, $currency, true),
                ARRAY_FILTER_USE_KEY
            ) : $allRates;

            $rates = [];
            foreach ($filteredRates as $currencyCode => $filteredRate) {
                $rates[$currencyCode] = new CurrencyRate(
                    $this->getSellRateWithCommission($filteredRate['sell']),
                    $this->getBuyRateWithCommission($filteredRate['buy']),
                    $currencyCode,
                );
            }

            return new Rates($rates);
        } catch (ClientExceptionInterface $e) {
            throw new \Exception('External service unavailable', previous: $e);
        } catch (\JsonException $e) {
            throw new \Exception('Unexpected response format from external service', previous: $e);
        }
    }
}