<?php

namespace App\Tests\Service\CurrencyConverter;

use App\Service\CurrencyConverter\CurrencyConverter;
use App\Service\Exchange\ExchangeRateProviderInterface;
use App\Service\Exchange\Rates;
use App\Service\Exchange\ValueObject\CurrencyRate;
use phpDocumentor\Reflection\Types\Static_;
use PHPUnit\Framework\TestCase;

class CurrencyConverterTest extends TestCase
{
    private CurrencyConverter $service;

    protected function setUp(): void
    {
        parent::setUp();

        $rates = json_decode(
            file_get_contents(dirname(__DIR__, 2) . '/resources/blockchain-info-ticker-response.json'),
            true
        );

        $rateProvider = $this->createMock(ExchangeRateProviderInterface::class);
        $rateProvider->method('getRates')->willReturnCallback(static function () use ($rates) {
            $currency = func_get_arg(0);

            $filteredRates = [];
            foreach ($rates as $currencyCode => $rate) {
                if (false === in_array($currencyCode, $currency, true)) {
                    continue;
                }

                $filteredRates[$currencyCode] = new CurrencyRate($rate['sell'], $rate['buy'], $currencyCode);
            }

            return new Rates($filteredRates);
        });

        $this->service = new CurrencyConverter($rateProvider);
    }

    /** @dataProvider getConvertVariants */
    public function testConvert(
        $currencyFrom,
        $currencyTo,
        $value,
        $expectedValueFrom,
        $expectedValueTo,
        $expectedRate
    ): void {
        $result = $this->service->convert($currencyFrom, $currencyTo, $value);

        $this->assertSame($expectedValueFrom, $result->valueFrom);
        $this->assertSame($expectedValueTo, $result->valueTo);
        $this->assertSame($expectedRate, $result->rate);
    }

    public function getConvertVariants(): \Generator
    {
        $file = new \SplFileObject(dirname(__DIR__, 2) . '/resources/currencyConvertVariants.csv', 'r');

        $file->fgetcsv(); // Skip headers

        while (false === $file->eof()) {
            yield $file->fgetcsv();
        }
    }
}
