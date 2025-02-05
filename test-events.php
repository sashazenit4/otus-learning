<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$elementFields = [
    'TITLE' => '123',
    'IBLOCK_ID' => 2,
];

$eventHandler = \Otus\Events\EventHandlerFactory::create($elementFields['IBLOCK_ID']);

$eventHandler->onBeforeAdd($elementFields);

$model = new \CIBlockElement();
$model->Add($elementFields);

$eventHandler->onAfterAdd($elementFields);
