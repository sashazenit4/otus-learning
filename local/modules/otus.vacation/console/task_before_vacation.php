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

Loader::includeModule('intranet');

$arFilter = array(
    "PER_USER" => true,
    "USERS" => false,
    'SELECT' => ['DATE_ACTIVE_FROM', 'DATE_ACTIVE_TO']
);

$arAbsence = \CIntranetUtils::GetAbsenceData($arFilter);

$actualAbsentUser = [];

foreach ($arAbsence as $userId => $absenceAr) {
    foreach ($absenceAr as $absence) {
        if ($absence['DATE_ACTIVE_FROM'] == date("d.m.Y", strtotime("+14 days"))) {
            $actualAbsentUser[$userId] = ['DATE_FROM' => $absence['DATE_ACTIVE_FROM'], 'DATE_TO' => $absence['DATE_ACTIVE_TO']];
        }
    }
}

// Подключаем модуль tasks
CModule::IncludeModule("tasks");

foreach ($actualAbsentUser as $userId => $absenceInfo) {
    // Устанавливаем параметры задачи
    $userHead = getUserHead($userId);
    $arFields = Array(
        "TITLE" => "Передать дела перед отпуском " . $absenceInfo['DATE_FROM'] . " - " . $absenceInfo['DATE_TO'],
        "DESCRIPTION" => "Перед отпуском передайте, пожалуйста, свои дела коллегам",
        "RESPONSIBLE_ID" => $userId,
        "DEADLINE" => ConvertTimeStamp(time() + 14*24*60*60, "FULL"),
        "CREATED_BY" => empty($userHead) ? $userId : $userHead,
    );

// Добавляем задачу в Битрикс
    \CTaskItem::add($arFields, $userId);
}

function getUserHead ($userId): int
{
    $userInfo = \CUser::getByID($userId)->fetch();
    $userDepartment = $userInfo['UF_DEPARTMENT'][0];
    $headId = \CIntranetUtils::GetDepartmentManagerID($userDepartment);
    return $headId;
}