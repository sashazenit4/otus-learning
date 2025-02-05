<?php
define('DEBUG_FILE_NAME', $_SERVER["DOCUMENT_ROOT"] .'/logs/'.date("Y-m-d").'.log');

if (file_exists(__DIR__ . '/classes/autoload.php')) {
    require_once __DIR__ . '/classes/autoload.php';
}

//\Otus\Diagnostic\Helper::writeToLog('Hello, world!');

\Bitrix\Main\EventManager::getInstance()->addEventHandler('', 'ColorsOnDelete', ['\Otus\Highload\Handler', 'onColorAdd']);
function agentDebug($arAgent, $strEvent, $strEvalResult = null, $error = ""){
    \Bitrix\Main\Diag\Debug::dumpToFile([
        '$arAgent'       => $arAgent,
        '$strEvent'      => $strEvent,
        '$strEvalResult' => $strEvalResult,
        '$error'         => $error,
    ],
        "data",
        str_replace($_SERVER['DOCUMENT_ROOT'], "", __DIR__."/log.log")
    );
}
define('BX_AGENTS_LOG_FUNCTION', 'agentDebug');
