<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Application;

$cache = Cache::createInstance(); // Служба кеширования
$taggedCache = Application::getInstance()->getTaggedCache(); // Служба пометки кеша тегами

//необходим одинаковый путь в $cache->initCache() и  $taggedCache->startTagCache()
$cachePath = 'mycachepath';
$myTag = 'my_awesome_tag'; # b_cache_tag
$cacheTime = 30 * 60; // время кеширования, указывается в секундах
$cacheId = 'date_and_random_number_' . \Bitrix\Main\Engine\CurrentUser::get()->getId(); // формируем идентификатор кеша в зависимости от параметров

if ($cache->initCache($cacheTime, $cacheId, $cachePath))
{
    $vars = $cache->getVars();
    echo '<pre>'; var_dump($vars); echo '</pre>';
}
elseif ($cache->startDataCache())
{
    $taggedCache->startTagCache($cachePath);
    $vars = [
        'date' => date('r'),
        'rand' => rand(0, 9999), // Если данные закешированы - число не будет меняться
    ];

    $taggedCache->registerTag($myTag); // Добавляем теги

//    $cacheInvalid = false; // Если что-то пошло не так и решили кеш не записывать
//    if ($cacheInvalid)
//    {
//        $taggedCache->abortTagCache();
//        $cache->abortDataCache();
//    }

    // записываем кеш
    $taggedCache->endTagCache();
    $cache->endDataCache($vars);
}

//$taggedCache = Application::getInstance()->getTaggedCache(); // Служба пометки кеша тегами
//$taggedCache->clearByTag($myTag); //сброс
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");