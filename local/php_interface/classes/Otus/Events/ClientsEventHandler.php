<?php
namespace Otus\Events;

class ClientsEventHandler implements OnBeforeAddEventHandlerInterface, OnAfterAddEventHandlerInterface
{
    public function onBeforeAdd(&$element)
    {
        $element['TITLE'] = 'OTUS - ' . $element['TITLE'];
    }

    public function onAfterAdd(&$element)
    {
        // отправь уведомление
    }
}
