<?php

namespace App\Etl\Transformers;

use App\Utility\Text;
use Cake\I18n\FrozenTime;
use Cake\ORM\Locator\LocatorAwareTrait;
use Marquine\Etl\Row;
use Marquine\Etl\Transformers\Transformer;

class LocationTransformer extends Transformer {

    use LocatorAwareTrait;

    public function transform(Row $row) {
        $data = [];
        $source = $row->toArray();

        /** Source Cleanup */
        foreach (array_keys($source) as $key) {
            $row->remove($key);
        }

        $data += [
            'uuid'          => mb_strtolower($source['LocationID']),
            'name'          => Text::slug($source['Name']),
            'label'         => trim((string)$source['Name']),
            'code'          => $source['DisplayCode'],
            'description'   => trim((string)$source['Description']) ?: null,
            'active'        => $source['Active'],
            'enabled'       => $source['Sellable'] ?? false,
            'financial'     => false,
            'created'       => $row['created-dt'] ?? new FrozenTime(),
            'modified'      => $row['modified-dt'] ?? new FrozenTime(),
        ];

        /** Set new ROW properties and cleanup data array */
        foreach ($data as $key=>$value) {
            $row->set($key, $value);
            unset($data[$key]);
        }
    }
}
