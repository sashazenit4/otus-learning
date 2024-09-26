<?php

namespace Otus\Vacation\Internal;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\DateTimeField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class VacationRequestApprovalTable extends DataManager
{
    public static function getTableName()
    {
        return 'f_vacation_request_approval';
    }

    public static function getMap()
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new DateTimeField('CREATED_AT'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_REQUEST_APPROVAL_TABLE_FIELD_CREATED_AT')),

            (new DateTimeField('UPDATE_AT'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_REQUEST_APPROVAL_TABLE_FIELD_UPDATE_AT')),

            (new IntegerField('APPROVAL_ID'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_REQUEST_APPROVAL_TABLE_FIELD_APPROVAL_ID')),

            (new IntegerField('REQUEST_ID'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_REQUEST_APPROVAL_TABLE_FIELD_REQUEST_ID')),

            (new StringField('STATUS'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_REQUEST_APPROVAL_TABLE_FIELD_STATUS')),

            (new StringField('APPROVAL_TYPE'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_REQUEST_APPROVAL_TABLE_FIELD_APPROVAL_TYPE')),

            (new TextField('DESCRIPTION'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_REQUEST_APPROVAL_TABLE_FIELD_DESCRIPTION')),
        ];
    }
}
