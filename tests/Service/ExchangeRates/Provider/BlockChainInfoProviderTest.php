<?php

namespace App\Tests\Service\ExchangeRates\Provider;

use App\Service\ExchangeRates\Enum\ExchangeMethod;
use App\Service\ExchangeRates\Provider\BlockChainInfoExchangeRateProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class BlockChainInfoProviderTest extends TestCase
{
    public function testGetPrices()
    {
        $tickerResponse = file_get_contents(dirname(__DIR__, 3) . '/resources/blockchain-info-ticker-response.json');
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], $tickerResponse),
            new Response(200, ['Content-Type' => 'application/json'], $tickerResponse),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $provider = new BlockChainInfoExchangeRateProvider($client, new ParameterBag(['service_commission_percent' => 2]));

        $prices = $provider->getRates(ExchangeMethod::Sell);

        self::assertSame(42646.302, $prices['AUD']);

        $prices = $provider->getRates(ExchangeMethod::Buy);

        self::assertSame(40973.898, $prices['AUD']);
    }
}
