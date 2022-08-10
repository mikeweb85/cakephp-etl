<?php

namespace App\Etl\Extractors;

use Cake\ORM\Locator\LocatorAwareTrait;
use Marquine\Etl\Extractors\Extractor;

class RabbitMqExtractor extends Extractor {

    use LocatorAwareTrait;

    protected $availableOptions = [
        'hydration', 'buffer', 'sort', 'direction', 'finder',
        'contain', 'conditions', 'fields', 'autofields', 'limit', 'offset',
    ];

    public function extract() {
        // TODO: Implement extract() method.
    }
}
