<?php
if (php_sapi_name() != 'cli')
{
    die();
}

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define("BX_NO_ACCELERATOR_RESET", true);
define("BX_CRONTAB", true);
define("STOP_STATISTICS", true);
define("NO_AGENT_STATISTIC", "Y");
define("DisableEventsCheck", true);
define("NO_AGENT_CHECK", true);

$_SERVER['DOCUMENT_ROOT'] = realpath('/home/bitrix/www');
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

/** Include bitrix core */
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";

use Bitrix\Main\Loader;

// Подключаем модуль tasks
CModule::IncludeModule("tasks");

$rsUsers = \Bitrix\Main\UserTable::getList([]);

$users = [];

while ($user = $rsUsers->fetch()) {
    $users[$user['ID']] = $user['LAST_NAME'] . ' ' . $user['NAME'] . ' ' . $user['SECOND_NAME'];
}

foreach ($users as $userId => $userName) {
    // Устанавливаем параметры задачи
    $arFields = Array(
        "TITLE" => "Планирование ежегодного  отпуска " . $userName,
        "DESCRIPTION" => "Пожалуйста, запланируйте отпуск на следующий год  в разделе <a href='/vacation_request/'>отпуска</a>.
        Не забывайте, что:
        - Хотя бы для одного из выбранных отпусков число в поле «Количество дней в отпуске» должно быть больше или равно 14.
        - Суммарно дней для всех выбранных отпусков должно быть строго 28.",
        "RESPONSIBLE_ID" => $userId,
        "DEADLINE" => ConvertTimeStamp(time() + 5*24*60*60, "FULL"),
        "CREATED_BY" => $userId,
    );

// Добавляем задачу в Битрикс
    \CTaskItem::add($arFields, $userId);
}
