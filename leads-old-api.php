<?php

use Bitrix\Main\Loader;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

if (!Loader::includeModule('crm')) {
    return;
}
$leadOrder = [
    'TITLE' => 'ASC',
];
$leadFilterFields = [
    'ID' => [1, 2 , 3],
];
$leadGroupBy = false;
$leadNavStartParams = false;
$selectFields = [
    'ID',
    'TITLE',
    'UF_CRM_MY_CUSTOM_FIELD',
];
$rawLeadList = \CCrmLead::GetListEx(
    $leadOrder,
    $leadFilterFields,
    $leadGroupBy,
    $leadNavStartParams,
    $selectFields
);

while ($lead = $rawLeadList->fetch()) {
    var_dump($lead);
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
