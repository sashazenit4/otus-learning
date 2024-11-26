<?php

use Bitrix\Main\Loader;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

Loader::includeModule('crm');
$contactFields = [
    'NAME' => 'Александр',
    'LAST_NAME' => 'Холин',
];
$contactsModel = new \CCrmContact;
$newContactId = $contactsModel->Add($contactFields);

$phone = '+79999999999';
$cont = [
    [
        'ENTITY_ID' => 'CONTACT',   // Тип сущности - контакт
        'ELEMENT_ID' => $newContactId,   // ID Контакта
        'TYPE_ID' => 'PHONE',
        'VALUE_TYPE' => 'WORK',
        'VALUE' => $phone,      // Номер телефона
        'COUNTRY_CODE' => 'RU',
    ],
    [
        'ENTITY_ID' => 'CONTACT',   // Тип сущности - контакт
        'ELEMENT_ID' => $newContactId,   // ID Контакта
        'TYPE_ID' => 'PHONE',
        'VALUE_TYPE' => 'WORK',
        'VALUE' => '+78888888888',// Номер телефона
        'COUNTRY_CODE' => 'RU',
    ],
];

$multi = new CCrmFieldMulti();
foreach ($cont as $item) {
    $multi->Add($item);
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
