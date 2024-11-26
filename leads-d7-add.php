<?php

use Bitrix\Main\Loader;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

Loader::includeModule('crm');

$leadFields = [
    'TITLE' => 'TEST-lead-' . date('d-m-Y-H-i-s'),
    'MODIFY_BY_ID' => 1,
    'ASSIGNED_BY_ID' => 1,
];

$res = \Bitrix\Crm\LeadTable::add($leadFields);
var_dump($res);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
