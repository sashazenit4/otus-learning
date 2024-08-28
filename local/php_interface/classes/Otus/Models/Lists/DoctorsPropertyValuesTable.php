<?php

namespace Otus\Models\Lists;

use Otus\Models\AbstractIblockPropertyValuesTable;
use Bitrix\Main\ORM\Fields\ExpressionField;

class DoctorsPropertyValuesTable extends AbstractIblockPropertyValuesTable
{
    const IBLOCK_ID = 16;
    public static function getMap(): array
    {
        $map['PROCEDURES'] = new ExpressionField(
            'PROCEDURES',
            sprintf('(select group_concat(e.ID, ";", e.NAME SEPARATOR "\0") as VALUE from %s as m join b_iblock_element as e on m.VALUE = e.ID where m.IBLOCK_ELEMENT_ID = %s and m.IBLOCK_PROPERTY_ID = %d)',
                static::getTableNameMulti(),
                '%s',
                static::getPropertyId('PROCEDURES_ID')
            ),
            ['IBLOCK_ELEMENT_ID'],
            ['fetch_data_modification' => [static::class, 'getMultipleFieldIdValueModifier']]
        );

        return parent::getMap() + $map;
    }
}
