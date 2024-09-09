<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$cacheTime = 30 * 60; // время кеширования, указывается в секундах
$cacheId = 'grid_football_teams1_' . \Bitrix\Main\Engine\CurrentUser::get()->getId(); // формируем идентификатор кеша в зависимости от параметров

// создаем объект
$obCache = new CPHPCache; // если кеш есть и он ещё не истек, то

if ($obCache->InitCache($cacheTime, $cacheId)) {
    // получаем закешированные переменные
    $arResult = $obCache->GetVars()['RESULT'];

} else {
    $arResult = [
        'REAL_MADRID' => ['Toni Kroos', 'Lika Modric', 'Federico Valverde'],
        'FC_BAYERN' => ['Thomas Müller', 'Leon Goretzka', 'Joshua Kimmich']
    ];

    if ($obCache->StartDataCache()) {
        // записываем данные в файл кеша
        $obCache->EndDataCache(['RESULT' => $arResult]);
    }

}
//$obCache->CleanDir(); //сброс
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");