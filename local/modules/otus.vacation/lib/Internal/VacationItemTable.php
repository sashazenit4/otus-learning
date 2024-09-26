<?php

namespace Otus\Vacation\Internal;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\DateTimeField;
use Bitrix\Main\ORM\Fields\DateField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class VacationItemTable extends DataManager
{
    public static function getTableName()
    {
        return 'f_vacation_item';
    }

    public static function getMap()
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new DateTimeField('CREATED_AT'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_ITEM_TABLE_FIELD_CREATED_AT')),

            (new DateTimeField('UPDATE_AT'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_ITEM_TABLE_FIELD_UPDATE_AT')),

            (new IntegerField('REQUEST_ID'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_ITEM_TABLE_FIELD_REQUEST_ID')),

            (new StringField('STATUS'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_ITEM_TABLE_FIELD_STATUS')),

            (new DateField('DATE_FROM'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_ITEM_TABLE_FIELD_DATE_FROM')),

            (new DateField('DATE_TO'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_ITEM_TABLE_FIELD_DATE_TO')),
        ];
    }
}
