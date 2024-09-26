<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('MAIN_PAGE_TITLE'));

try{
    $APPLICATION->IncludeComponent('otus:vacation', '', [
        'SEF_MODE' => 'Y',
        'SEF_FOLDER' => '/vacation_request/',
        'SEF_URL_TEMPLATES' => array(
            'vacation_grid' => '/',
            'vacation_form' => '#ID#/',
            'vacation_change_item' => 'vacation_change_item/0/'
        ),
    ]);
    

} catch (\Throwable $e) {
    ShowError($e->getMessage());
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>