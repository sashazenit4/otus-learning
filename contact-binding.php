<?php

use Bitrix\Main\Loader;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

Loader::includeModule('crm');
$deal = new \CCrmDeal();
$newDealId = $deal->Add([
    'COMPANY_ID' => 1,
    // Для привязки контакта можно передать любой из ключей ниже. Если передана один, другие не нужны
    'CONTACT_ID' => 12, // Привязка одного контакта
    'CONTACT_IDS' => [1, 2, 3], // Привязка нескольких контактов. Первый контакт будет сохранен как основной
    'CONTACT_BINDINGS' => [ // Привязка нескольких контактов. Позволяет в явном виде задать основной контакт, сортировку и др
        'CONTACT_ID' => 1,
        'SORT' => 10,
        'ROLE_ID' => 0,
        'IS_PRIMARY' => 'Y',
    ],
    [
        'CONTACT_ID' => 2,
        'SORT' => 20,
    ],
]);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';