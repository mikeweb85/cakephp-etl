<?php

namespace App\Etl\Extractors;

trait PathspecExtratorTrait {

    protected function transpose(array $columns): array {
        $data = [];

        foreach ($columns as $column => $items) {
            foreach ($items as $row => $item) {
                $data[$row][$column] = $item;
            }
        }

        return $data;
    }
}
