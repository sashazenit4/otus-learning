<?php
namespace Otus\Highload;

use Bitrix\Main\Event;

class Handler
{
    public static function onColorAdd(Event $event): void
    {
        $parameters = $event->getParameters();
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/hl.log', var_export($parameters, true) . PHP_EOL, FILE_APPEND);
    }
}
