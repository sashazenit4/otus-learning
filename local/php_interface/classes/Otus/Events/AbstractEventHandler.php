<?php
namespace Otus\Events;

abstract class AbstractEventHandler
{
    public static function dispatch(string $eventType, array &$element)
    {
        $handler = EventHandlerFactory::create($element['IBLOCK_ID']);

        if ($handler && method_exists($handler, $eventType)) {
            $handler->$eventType($element);
        }
    }

    public static function onBeforeAddDispatcher(&$element)
    {
        self::dispatch('onBeforeAdd', $element);
    }

    public static function onAfterAddDispatcher(&$element)
    {
        self::dispatch('onAfterAdd', $element);
    }
}
