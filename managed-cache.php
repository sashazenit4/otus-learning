<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$managedCache = Bitrix\Main\Application::getInstance()->getManagedCache();

$uniqId = 'my_cache_key'; // ключ, по которому сохраняем и получаем данные
$ttl = 30; // время жизни кеша
# в папке /bitrix/managed_cache а не в папке /bitrix/cache
$managedCache->read($ttl, $uniqId); // считываем кеш с диска

$res = $managedCache->get($uniqId); // получаем данные из считанного файла
if ($res === false) { // если данных нет
    $res = date('r'); // то генерируем данные, тут, например, у нас тяжелая логика

    $managedCache->set($uniqId, $res); // сохраняем данные (пока будут храниться в памяти)
}

var_dump($res); // работаем с данными

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");