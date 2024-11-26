<?php

use Bitrix\Main\Loader;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

Loader::includeModule('crm');

$leadFields = [
    'TITLE' => 'TEST-' . date('d-m-Y-H-i-s'),
    'UF_CRM_1732637027' => [
        'TEST',
        'TEST2',
    ],
];
$leadModel = new \CCrmLead;
$res = $leadModel->add($leadFields);
var_dump($res);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
