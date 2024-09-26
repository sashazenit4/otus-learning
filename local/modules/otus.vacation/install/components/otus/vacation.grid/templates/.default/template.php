<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\UI\Extension;

Loader::includeModule('ui');
Extension::load(['sidepanel']);
Loc::loadMessages(__FILE__);
?>

<?php

/**
 * Toolbar filter
 **/
\Bitrix\UI\Toolbar\Facade\Toolbar::addFilter([
    'FILTER_ID' => $arResult['GRID_ID'],
    'GRID_ID' => $arResult['GRID_ID'],
    'FILTER' => $arResult['GRID_FILTER'],
    'ENABLE_LIVE_SEARCH' => true,
    'ENABLE_LABEL' => true
]); ?>

<?php

/**
 * Toolbar buttons
 **/
if ($arResult['permissions']['CAN_CREATE_VACATION_REQUEST']) {
    $addButton = new \Bitrix\UI\Buttons\AddButton([
        "click" => new \Bitrix\UI\Buttons\JsCode(
            "BX.Otus.Vacation.List.showCreateRequestForm()"
        ),
        "text" => Loc::getMessage('CREATE_VACATION_REQUEST_BUTTON_TITLE')
    ]);

    \Bitrix\UI\Toolbar\Facade\Toolbar::addButton($addButton);
}

$addButton = new \Bitrix\UI\Buttons\AddButton([
    "click" => new \Bitrix\UI\Buttons\JsCode(
        "BX.Otus.Vacation.List.showChangeItemForm()"
    ),
    "color" => \Bitrix\UI\Buttons\Color::PRIMARY_DARK,
    "text" => Loc::getMessage('CHANGE_VACATION_ITEM_BUTTON_TITLE')
]);

\Bitrix\UI\Toolbar\Facade\Toolbar::addButton($addButton);
?>

<?php $APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
    'GRID_ID' => $arResult['GRID_ID'],
    'COLUMNS' => $arResult['GRID_COLUMNS'],
    'ROWS' => $arResult['ROWS'],
    'NAV_OBJECT' => $arResult['NAV'],
    'AJAX_MODE' => 'Y',
    'AJAX_ID' => \CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
    'AJAX_OPTION_JUMP' => 'N',
    'SHOW_ROW_CHECKBOXES' => false,
    'SHOW_CHECK_ALL_CHECKBOXES' => false,
    'SHOW_ROW_ACTIONS_MENU' => true,
    'SHOW_GRID_SETTINGS_MENU' => true,
    'SHOW_NAVIGATION_PANEL' => true,
    'SHOW_PAGINATION' => true,
    'SHOW_SELECTED_COUNTER' => false,
    'SHOW_TOTAL_COUNTER' => false,
    'SHOW_PAGESIZE' => false,
    'SHOW_ACTION_PANEL' => false,
    'ALLOW_COLUMNS_SORT' => true,
    'ALLOW_COLUMNS_RESIZE' => true,
    'ALLOW_HORIZONTAL_SCROLL' => true,
    'ALLOW_SORT' => true,
    'ALLOW_PIN_HEADER' => true,
    'AJAX_OPTION_HISTORY' => 'N',
    "ENABLE_COLLAPSIBLE_ROWS" => true
], $component); ?>
