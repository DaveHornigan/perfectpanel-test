<?php

namespace App\Service\Exchange;

use App\Service\Exchange\ValueObject\CurrencyRate;
use ArrayObject;

class Rates extends ArrayObject
{
    /** @param array<string, CurrencyRate> $rates */
    public function __construct(private readonly array $rates) {
        parent::__construct($this->rates);
    }
}