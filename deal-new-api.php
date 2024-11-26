<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

use Bitrix\Main\Loader;
use Bitrix\Crm\Service\Container;

if (!Loader::includeModule('crm')) {
    return;
}
$dealOrder = [
    'TITLE' => 'ASC',
];
$dealFilterFields = [];
$dealSelectFields = [
    'ID',
    'TITLE',
];
$dealFactory = Container::getInstance()->getFactory(\CCrmOwnerType::Deal);

$dealItems = $dealFactory->getItems([
    'filter' => $dealFilterFields,
    'order' => $dealOrder,
    'select' => $dealSelectFields,
]);

echo '<pre>';
foreach ($dealItems as $dealItem) {
    var_dump($dealItem['ID']); # 1
    var_dump($dealItem['TITLE']);
    var_dump($dealItem->get('ID')); # 2
    var_dump($dealItem->get('TITLE'));
    var_dump($dealItem->getId()); # 3
    var_dump($dealItem->getTitle());
}
echo '</pre>';
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
