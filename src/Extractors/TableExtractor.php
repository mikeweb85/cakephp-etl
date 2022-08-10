<?php

namespace App\Etl\Extractors;

use Cake\ORM\Locator\LocatorAwareTrait;
use Marquine\Etl\Extractors\Extractor;
use Cake\ORM\Table as OrmTable;
use Marquine\Etl\Row;
use Generator;

class TableExtractor extends Extractor {

    use LocatorAwareTrait;

    const SORT_ASC = 'ASC';

    const SORT_DESC = 'DESC';

    /**
     * @var OrmTable
     */
    protected $input;

    /**
     * @var bool
     */
    protected bool $hydration = false;

    /**
     * @var bool
     */
    protected bool $autofields = true;

    /**
     * @var bool
     */
    protected bool $buffer = false;

    /**
     * @var string
     */
    protected string $sort = '';

    /**
     * @var string
     */
    protected string $direction = 'asc';

    /**
     * @var string
     */
    protected string $finder = 'all';

    /**
     * @var string[]
     */
    protected array $contain = [];

    /**
     * @var array|callable
     */
    protected $conditions = [];

    /**
     * @var array
     */
    protected array $fields = [];

    /**
     * @var int
     */
    protected int $limit = 0;

    /**
     * @var int
     */
    protected int $offset = 0;

    /**
     * @param OrmTable|string $input
     * @return void
     */
    public function setInput(OrmTable|string $input): void {
        if (is_string($input)) {
            $input = $this->fetchTable($input);
        }

        $this->input = $input;
    }

    protected $availableOptions = [
        'hydration', 'buffer', 'sort', 'direction', 'finder',
        'contain', 'conditions', 'fields', 'autofields', 'limit', 'offset',
    ];

    public function extract(): Generator {
        if (!$this->sort) {
            $this->sort = $this
                ->input
                ->getPrimaryKey();
        }

        $this->direction = mb_strtoupper($this->direction);

        $query = $this
            ->input
            ->find($this->finder)
            ->contain($this->contain)
            ->select($this->fields)
            ->enableAutoFields($this->autofields)
            ->enableHydration($this->hydration)
            ->enableBufferedResults($this->buffer);

        if ($this->direction == static::SORT_ASC) {
            $query->orderAsc($this->sort, true);

        } elseif ($this->direction == static::SORT_DESC) {
            $query->orderDesc($this->sort, true);
        }

        if ($this->limit > 0) {
            $query->limit($this->limit);
        }

        if ($this->offset > 0) {
            $query->offset($this->offset);
        }

        if (!empty($this->conditions)) {
            $query->where($this->conditions);
        }

        foreach ($query->all() as $entity) {
            yield new Row((array)$entity);
        }

        unset($query);
    }
}
