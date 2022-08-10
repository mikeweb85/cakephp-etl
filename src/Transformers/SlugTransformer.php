<?php

namespace App\Etl\Transformers;

use App\Utility\Text;
use Marquine\Etl\Row;
use Marquine\Etl\Transformers\Transformer;

class SlugTransformer extends Transformer {

    /**
     * Transformer columns.
     * @var string[]
     */
    protected array $columns = [];

    /**
     * Properties that can be set via the options method.
     * @var array
     */
    protected $availableOptions = [
        'columns',
    ];

    public function transform(Row $row) {
        $row->transform($this->columns, function($value) {
            return Text::slug((string)$value);
        });
    }
}
