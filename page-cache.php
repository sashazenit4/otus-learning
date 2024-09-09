<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$cacheTime = 30 * 60; // время кеширования, указывается в секундах
$cacheId = 'random_page_cache_' . \Bitrix\Main\Engine\CurrentUser::get()->getId(); // формируем идентификатор кеша в зависимости от параметров

$obCache = new CPageCache; // создаем объект

if ($obCache->InitCache($cacheTime, $cacheId)) {
    $obCache->Output();
} elseif($obCache->StartDataCache($cacheTime, $cacheId))
{
    echo "<div style='background: red; width: 100px; height: 100px'>Hello, HTML cache!</pre>";
    // записываем предварительно буферизированный вывод в файл кеша
    $obCache->EndDataCache();
}

//$obCache->CleanDir(); //сброс
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");