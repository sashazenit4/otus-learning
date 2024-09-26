<?php
namespace Otus\Vacation\Crm;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

class Handlers
{
    public static function updateTabs(Event $event): EventResult
    {
        $entityTypeID = $event->getParameter('entityTypeID');
        $tabs = $event->getParameter('tabs');
        $lastTab = end($tabs);
        var_dump($lastTab);

        if ($entityTypeID === \CCrmOwnerType::Deal) {
            $lastTab['id'] = 'history_copied_tab';
            $lastTab['name'] = 'Свой контент';
            $lastTab['html'] = 'Свой контент';
            $tabs[] = $lastTab;
        }

        return new EventResult(EventResult::SUCCESS, [
            'tabs' => $tabs,
        ]);
    }
}
