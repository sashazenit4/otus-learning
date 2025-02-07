<?php
namespace Otus\Events;

class EventHandlerFactory
{
    public const CLIENTS_IBLOCK_ID = 2;
    public static function create($iblockId)
    {
        $handlers = [
            self::CLIENTS_IBLOCK_ID => ClientsEventHandler::class
        ];

        if (!isset($handlers[$iblockId])) {
            error_log("Обработчик событий для IBLOCK_ID {$iblockId} не найден");
            return null;
        }

        $handlerClass = $handlers[$iblockId];

        return new $handlerClass();
    }
}
