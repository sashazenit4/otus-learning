<?php
namespace Aholin\Crm\Handlers;

use Bitrix\Main\Event;

class Deal
{
    public static function handleBeforeUpdate(Event $event)
    {
        $parameters = $event->getParameters();
        $fields = $parameters['FIELDS'];
        if ($fields['STAGE_ID'] == 'C2:PREPARATION') {
            $fields['STAGE_ID'] = 'NEW';
            $event->setParameter('FIELDS', $fields);
        }
        return $event;
    }
}