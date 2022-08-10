<?php

namespace App\Etl\Extractors;

use Marquine\Etl\Extractors\Extractor;
use Cake\Database\Query;
use Marquine\Etl\Row;
use Generator;

class DatabaseQueryExtractor extends Extractor {

    /** @var bool */
    protected bool $buffer = false;

    /** @var string[]  */
    protected $availableOptions = [
        'buffer',
    ];

    public function extract(): Generator {
        /** @var Query $query */
        $query = clone $this->input;

        $statement = $query
            ->enableBufferedResults($this->buffer)
            ->execute();

        while ($row = $statement->fetch('assoc')) {
            yield new Row($row);
        }

        unset($statement, $query);
    }
}
