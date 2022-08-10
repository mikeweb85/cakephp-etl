<?php

namespace App\Etl\Transformers;

use App\Utility\Text;
use Marquine\Etl\Row;
use Marquine\Etl\Transformers\Transformer;

class ValueListEntityTransformer extends Transformer {

    public function transform(Row $row) {
        $source = $row->toArray();

        /** Source cleanup */
        foreach (array_keys($source) as $key) {
            $row->remove($key);
        }

        $row->set('uuid', mb_strtolower($source['ValueListEntryID']));
        $row->set('name', Text::slug($source['Entry']));
        $row->set('label', $source['Entry']);
        $row->set('description', trim((string)$source['Description']) ?: null);
        $row->set('enabled', (bool)$source['Active']);

        if (isset($row['created-dt'])) {
            $row->set('created', $row['created-dt']);
        }

        if (isset($row['modified-dt'])) {
            $row->set('modified', $row['modified-dt']);
        }
    }
}
