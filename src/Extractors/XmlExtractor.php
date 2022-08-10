<?php

namespace App\Etl\Extractors;

use Marquine\Etl\Extractors\Extractor;

class XmlExtractor extends Extractor {

    use XpathExtractorTrait,
        PathspecExtratorTrait;

    public function extract() {
        // TODO: Implement extract() method.
    }
}
