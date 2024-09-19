<?php
define('DEBUG_FILE_NAME', $_SERVER["DOCUMENT_ROOT"] .'/logs/'.date("Y-m-d").'.log');

if (file_exists(__DIR__ . '/classes/autoload.php')) {
    require_once __DIR__ . '/classes/autoload.php';
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

\Bex\Monolog\MonologAdapter::loadConfiguration();