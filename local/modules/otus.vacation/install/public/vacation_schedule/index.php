<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Loader;

global $USER;
global $APPLICATION;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('MAIN_PAGE_TITLE'));

Loader::includeModule('otus.vacation');

$serviceLocator = ServiceLocator::getInstance();

$accessManager = $serviceLocator->get('otus.vacation.accessManager');

if ($accessManager->isUserAccountant($USER->getId()) || $USER->isAdmin()) {
    $controlsDepartmentsOn = true;
} else {
    $controlsDepartmentsOn = false;
}

try{
    $APPLICATION->IncludeComponent("otus:vacation.schedule", ".default", Array(
        'CONTROLS_DEPARTMENT_ON' => $controlsDepartmentsOn,
    ));
} catch (\Throwable $e) {
    ShowError($e->getMessage());
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
