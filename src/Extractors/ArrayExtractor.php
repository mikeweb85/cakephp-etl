<?php

namespace App\Etl\Extractors;

use Marquine\Etl\Extractors\Extractor;
use Cake\Utility\Hash;
use Marquine\Etl\Row;
use Generator;

class ArrayExtractor extends Extractor {

    /** @var array */
    protected $input;

    protected string $path = '';

    protected array $columns = [];

    /** @var string[]  */
    protected $availableOptions = [
        'path', 'columns',
    ];

    /**
     * @return Generator
     */
    public function extract(): Generator {
        $rows = ($this->path) ?
            Hash::extract($this->input, $this->path) :
            $this->input;

        foreach ($rows as $row) {
            if (!empty($this->columns)) {
                $row = array_intersect_key($row, array_flip($this->columns));
            }

            yield new Row($row);
        }
    }
}
