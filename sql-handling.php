<?php

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;
use Bitrix\Main\Application;

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';
/**
 * @var CMain $APPLICATION
 */
$APPLICATION->setTitle('Отладка SQL');

Loader::includeModule('iblock');

Application::getConnection()->startTracker();

$query = \Bitrix\Iblock\ElementTable::getList([
    'filter' => [
        'IBLOCK_ID' => 1,
    ],
    'select' => [
        'ID'
    ],
]);

Application::getConnection()->stopTracker();
Debug::dump($query->getTrackerQuery()->getSql());

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
