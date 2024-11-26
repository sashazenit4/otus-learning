<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

use Bitrix\Main\Loader;
use Bitrix\Crm\Service\Container;

if (!Loader::includeModule('crm')) {
    return;
}
$spOrder = [
    'TITLE' => 'ASC',
];
$spFilterFields = [];
$spSelectFields = [
    'ID',
    'TITLE',
];
$spFactory = Container::getInstance()->getFactory(1036);

$spItems = $spFactory->getItems([
    'filter' => $spFilterFields,
    'order' => $spOrder,
    'select' => $spSelectFields,
]);

var_dump($spItems[0]->get('ID'));

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
