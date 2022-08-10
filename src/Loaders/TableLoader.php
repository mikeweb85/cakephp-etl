<?php

namespace App\Etl\Loaders;

use App\Utility\Text;
use Cake\Core\Exception\CakeException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Locator\LocatorAwareTrait;
use Marquine\Etl\Loaders\Loader;
use Cake\ORM\Table as OrmTable;
use Marquine\Etl\Row;

class TableLoader extends Loader {

    use LocatorAwareTrait;

    /**
     * @var string
     */
    protected string $finder = 'all';

    /**
     * @var string
     */
    protected string $column = '';

    /**
     * @var bool
     */
    protected bool $transaction = false;

    /**
     * @var bool
     */
    protected bool $validate = true;

    /**
     * @var string[]
     */
    protected array $associated = [];

    /**
     * @var string[]
     */
    protected array $contain = [];

    /**
     * @var OrmTable
     */
    protected $output;

    /**
     * @var string[]
     */
    protected $availableOptions = [
        'finder', 'transaction', 'validate', 'contain', 'associated', 'column',
    ];

    /**
     * @param OrmTable|string $output
     * @return static
     */
    public function output($output): static {
        if (is_string($output)) {
            $output = $this
                ->getTableLocator()
                ->get($output);
        }

        $this->output = $output;

        return $this;
    }

    public function initialize() {
        if (!$this->column) {
            $this->column = $this
                ->output
                ->getPrimaryKey();
        }
    }

    public function finalize() {
    }

    public function load(Row $row) {
        $row = $row->toArray();

        if (empty($row)) {
            return;
        }

        try {
            $entity = $this
                ->output
                ->find($this->finder)
                ->contain($this->contain)
                ->where([
                    $this->column => $row[$this->column]
                ])
                ->firstOrFail();

        } catch(RecordNotFoundException $e) {
            $entity = @$this
                ->output
                ->newEntity($row, [
                    'associated'    => [],
                    'validate'      => $this->validate,
                ]);
        }

        if (!$entity->isNew()) {
            $this
                ->output
                ->patchEntity(
                    $entity,
                    $row, [
                    'associated'    => $this->associated,
                    'validate'      => $this->validate,
                ]);
        }

        $this
            ->output
            ->save(
                $entity, [
                    'associated'    => $this->associated,
                    'checkRules'    => $this->validate,
                    'atomic'        => $this->transaction,
                ]);
    }
}
