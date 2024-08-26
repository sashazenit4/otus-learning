<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$eventHandlers = [
    [
        'crm',
        'OnBeforeCrmDealUpdate',
        'Aholin\Crm\Handlers\Deal',
        'handleBeforeUpdate',
    ],
];

$eventManager = \Bitrix\Main\EventManager::getInstance();

foreach ($eventHandlers as $eventHandler) {
    $eventManager->addEventHandler($eventHandler[0], $eventHandler[1], [$eventHandler[2], $eventHandler[3]]);
}
