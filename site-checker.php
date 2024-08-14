<?php
require __DIR__ . '/local/php_interface/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

$date = date('Y-m-d');

$url = 'https://google.com';

$client = new Client();

try {
    $response = $client->request('GET', $url);

    $httpCode = $response->getStatusCode();

    $body = $response->getBody()->getContents();

    if ($httpCode == 200) {
        $message = "ОК";
    } else {
        $message = "Не ок";
    }

} catch (RequestException $e) {
    $message = "Произошла ошибка при попытке доступа к $url. Система может быть недоступна.";
}

echo $message;