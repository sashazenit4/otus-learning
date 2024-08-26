<?php
namespace Aholin\Crm;

use Bitrix\Crm;
use Bitrix\Main\Event;
use Bitrix\Main\ORM\Data\UpdateResult;

class DealTable extends Crm\DealTable
{
    public static function update($primary, array $data): UpdateResult
    {
        $event = new Event('crm', 'OnBeforeCrmDealUpdate', ['DEAL_ID' => $primary, 'FIELDS' => $data]);
        $event->send();
        $data = $event->getParameters();
        return parent::update($primary, $data);
    }
}
