<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
/**
 * @var CMain $APPLICATION
 */
$APPLICATION->SetTitle('Демонстрация Highload-блоки');
$APPLICATION->IncludeComponent('otus:highload.grid', '', [
    'CACHE_TYPE' => 'N',
    'CACHE_TIME' => 360000,
]);
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');