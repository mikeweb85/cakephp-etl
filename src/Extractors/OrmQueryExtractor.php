<?php

namespace App\Etl\Extractors;

use Marquine\Etl\Extractors\Extractor;
use Cake\ORM\Query;
use Marquine\Etl\Row;
use Generator;

class OrmQueryExtractor extends Extractor {

    /** @var bool */
    protected bool $buffer = false;

    /** @var bool */
    protected bool $hydration = false;

    /** @var bool */
    protected bool $autofields = false;

    /** @var string[]  */
    protected $availableOptions = [
        'hydration', 'buffer', 'autofields'
    ];

    public function extract(): Generator {
        /** @var Query $query */
        $query = clone $this->input;

        $query
            ->enableAutoFields($this->autofields)
            ->enableBufferedResults($this->buffer)
            ->enableHydration($this->hydration);


        foreach ($query->all() as $entity) {
            yield new Row((array)$entity);
        }

        unset($query);
    }
}
