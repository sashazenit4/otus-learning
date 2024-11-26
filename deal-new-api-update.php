<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

use Bitrix\Main\Loader;
use Bitrix\Crm\Service\Container;

if (!Loader::includeModule('crm')) {
    return;
}

$dealFactory = Container::getInstance()->getFactory(\CCrmOwnerType::Deal);
$existedDealId = 12;
$dealItem = $dealFactory->getItem($existedDealId);
$dealItem->set('TITLE', 'Тестовая сделка D7');
# $newDealItem->save(); Выполнит сохранение сразу без проверки прав доступа и без запуска обработчиков событий
$dealUpdateOperation = $dealFactory->getUpdateOperation($dealItem);

$updateResult = $dealUpdateOperation->launch();
echo '<pre>';
var_dump($updateResult->isSuccess());
echo '</pre>';

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
