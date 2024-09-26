<?php

namespace Otus\Vacation\Internal;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\DateTimeField;
use Bitrix\Main\ORM\Fields\DateField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class VacationItemApprovalTable extends DataManager
{
    public static function getTableName()
    {
        return 'f_vacation_item_approval';
    }

    public static function getMap()
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new DateTimeField('CREATED_AT'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_ITEM_APPROVAL_TABLE_FIELD_CREATED_AT')),

            (new IntegerField('CREATE_BY'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_ITEM_APPROVAL_TABLE_FIELD_CREATE_BY')),

            (new DateTimeField('UPDATE_AT'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_ITEM_APPROVAL_TABLE_FIELD_UPDATE_AT')),

            (new StringField('VACATION_ITEM_ID'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_ITEM_APPROVAL_TABLE_FIELD_VACATION_ITEM_ID')),

            (new StringField('APPROVAL_ID'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_ITEM_APPROVAL_TABLE_FIELD_VACATION_APPROVAL_ID')),

            (new StringField('APPROVAL_TYPE'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_ITEM_APPROVAL_TABLE_FIELD_APPROVAL_TYPE')),

            (new DateField('DATE_FROM'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_ITEM_APPROVAL_TABLE_FIELD_DATE_FROM')),

            (new DateField('DATE_TO'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_ITEM_APPROVAL_TABLE_FIELD_DATE_TO')),

            (new TextField('DESCRIPTION'))
                ->configureTitle(Loc::getMessage('OTUS_VACATION_ITEM_APPROVAL_TABLE_FIELD_DESCRIPTION')),
        ];
    }
}
