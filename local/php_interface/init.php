<?php
define('DEBUG_FILE_NAME', $_SERVER["DOCUMENT_ROOT"] .'/logs/'.date("Y-m-d").'.log');

if (file_exists(__DIR__ . '/classes/autoload.php')) {
    require_once __DIR__ . '/classes/autoload.php';
}
if (file_exists(__DIR__ . '/events.php')) {
    require_once __DIR__ . '/events.php';
}

\Otus\Diagnostic\Helper::writeToLog('Hello, world!');