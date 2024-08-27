<?php
namespace Otus\Orm;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;

class AuthorTable extends DataManager
{
    public static function getTableName()
    {
        return 'o_authors';
    }

    public static function getMap()
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new StringField('FIRST_NAME'))
                ->configureRequired(),

            (new StringField('LAST_NAME')),

            (new StringField('SECOND_NAME')),

            (new StringField('CITY')),
        ];
    }
}
