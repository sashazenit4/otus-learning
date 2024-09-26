<?php
if (! defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

global $APPLICATION;

$APPLICATION->IncludeComponent(
    'bitrix:ui.sidepanel.wrapper',
    '',
    [
        'USE_PADDING' => false,
        'POPUP_COMPONENT_NAME' => 'otus:vacation.form',
        'POPUP_COMPONENT_TEMPLATE_NAME' => '',
        "USE_UI_TOOLBAR" => "Y",
        'POPUP_COMPONENT_PARAMS' => [
            'REQUEST_ID' => $arResult['VARIABLES']['ID'],
            'TEMPLATE_PATH_LIST' => $arResult['TEMPLATE_PATH_LIST'],
        ]
    ]
);
