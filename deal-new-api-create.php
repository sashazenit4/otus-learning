<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

use Bitrix\Main\Loader;
use Bitrix\Crm\Service\Container;

if (!Loader::includeModule('crm')) {
    return;
}

$dealFactory = Container::getInstance()->getFactory(\CCrmOwnerType::Deal);
$newDealItem = $dealFactory->createItem();
$newDealItem->set('TITLE', 'Тестовая сделка D7 - ' . date('d-m-Y-H-i-s'));

//$res =  $newDealItem->save(); # Выполнит сохранение сразу без проверки прав доступа и без запуска обработчиков событий
//var_dump($res);
$dealAddOperation = $dealFactory->getAddOperation($newDealItem);

$addResult = $dealAddOperation->launch();
echo '<pre>';
var_dump($addResult);
echo '</pre>';

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
