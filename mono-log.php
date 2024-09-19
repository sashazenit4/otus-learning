<?php
use Monolog\Registry;

$logger = Registry::getInstance('feedback');

// Write info message with context: invalid message from feedback
$logger->info('Failed create new message on feedback form', array(
    'item_id' => 21,
    'Invalid data' => [], // error savings
    'Form data' => [] // data from feedback form
));
