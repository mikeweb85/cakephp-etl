<?php

namespace App\Etl\Extractors;

use Marquine\Etl\Extractors\Extractor;

class JsonExtractor extends Extractor {

    use JsonPathExtractorTrait,
        PathspecExtratorTrait;

    public function extract() {
        // TODO: Implement extract() method.
    }
}
