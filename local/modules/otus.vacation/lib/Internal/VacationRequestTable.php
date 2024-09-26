<?php

namespace Otus\Vacation\Internal;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\DateTimeField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class VacationRequestTable extends DataManager
{
    public static function getTableName()
    {
        return 'f_vacation_request';
    }

    public static function getMap()
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new DateTimeField('CREATED_AT'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_REQUEST_TABLE_FIELD_CREATED_AT')),

            (new DateTimeField('UPDATE_AT'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_REQUEST_TABLE_FIELD_UPDATE_AT'))
            ,
            (new StringField('STATUS'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_REQUEST_TABLE_FIELD_STATUS')),

            (new IntegerField('INITIATOR'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_REQUEST_TABLE_FIELD_INITIATOR')),

            (new IntegerField('REQUESTED_USER'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_REQUEST_TABLE_FIELD_REQUESTED_USER')),

            (new StringField('REQUEST_TYPE'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_REQUEST_TABLE_FIELD_REQUEST_TYPE')),

            (new StringField('VACATION_TYPE'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_REQUEST_TABLE_FIELD_VACATION_TYPE')),

            (new TextField('DESCRIPTION'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_REQUEST_TABLE_FIELD_DESCRIPTION')),

            (new BooleanField('IS_CHANGE_REQUEST'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_REQUEST_TABLE_FIELD_IS_CHANGE_REQUEST')),

            (new StringField('REPLACED_ITEM'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_REQUEST_TABLE_FIELD_REPLACED_ITEM')),
        ];
    }
}
