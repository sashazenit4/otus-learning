<?php
namespace Otus\Orm;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\FloatField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;

class PublishingTable extends DataManager
{
    public static function getTableName()
    {
        return 'o_publishing';
    }

    public static function getMap()
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new StringField('TITLE'))
                ->configureRequired(),

            (new StringField('CITY')),

            (new FloatField('AUTHOR_PROFIT'))
                ->configureRequired(),
        ];
    }
}

