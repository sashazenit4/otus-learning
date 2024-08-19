<?php

$logFile = __DIR__ . '/debug.log';

$currentDateTime = date('Y-m-d H:i:s');

$logEntry = "Подключение: " . $currentDateTime . PHP_EOL;

file_put_contents($logFile, $logEntry, FILE_APPEND);

echo "Date and time have been logged.";

