<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$cache = Bitrix\Main\Data\Cache::createInstance();
$cacheTime = 30 * 60; // время кеширования, указывается в секундах
$cacheId = 'grid_football_teams2_' . \Bitrix\Main\Engine\CurrentUser::get()->getId(); // формируем идентификатор кеша в зависимости от параметров
$cacheDir = '/default2';

if ($cache->initCache($cacheTime, $cacheId, $cacheDir)) # проверка существования кеша
{
    $result = $cache->getVars();
    echo '<pre>';
    var_dump($result);
    echo '</pre>';
}
elseif ($cache->startDataCache()) # инициализация создания нового кеша
{
    $result = [
        'Kurt Cobain',
        'Krist Novoselic',
        'Dave Grohl'
    ];
    $cache->endDataCache($result); # сохранения в кеш переменных
}

//$cache->clean($cacheId); //сброс по необходимости
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");