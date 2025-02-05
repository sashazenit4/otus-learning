<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->IncludeComponent('bitrix:news.list', '', [
    'IBLOCK_ID' => 16,
    'CACHE_TYPE' => 'A',
    'CACHE_TIME' => '3600',
]);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");