<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
/**
 * @var CMain $APPLICATION
 */
$APPLICATION->SetTitle('Демонстрация ORM');

$APPLICATION->IncludeComponent(
    'bitrix:crm.interface.toolbar',
    'title',
    [
        'TOOLBAR_ID' => 'CLIENT_VIEW',
        'BUTTONS' => [
            [
                'TEXT' => 'Выгрузить в Excel',
                'TITLE' => 'Выгрузить в Excel',
                'LINK' => '?template=excel',
                'ICON' => 'btn-export'
            ]
        ]
    ]
);

$APPLICATION->IncludeComponent('otus:sample.grid', '', [
    'CACHE_TYPE' => 'N',
    'CACHE_TIME' => 360000,
]);
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');