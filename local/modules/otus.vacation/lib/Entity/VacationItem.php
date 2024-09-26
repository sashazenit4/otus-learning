<?php

namespace Otus\Vacation\Entity;

use Bitrix\Main\Type\Date;
use Bitrix\Main\Type\DateTime;

/**
* Class VacationItem
* Сущность Отпуск
* @package Otus\Vacation\Entity
*/
class VacationItem
{
    /**
    * Идентификатор отпуска
    * @var int|null
    */
    public ?int $id = null; 

    /**
    * Когда создан
    * @var DateTime|null
    */
    public ?DateTime $createdAt = null; 

    /**
    * Когда обновлен
    * @var DateTime|null
    */
    public ?DateTime $updateAt = null; 
    
    /**
    * Идентификатор запроса на отпуск
    * @var int|null
    */
    public ?int $requestId = null;

    /**
    * Статус отпуска
    * @var string|null
    */
    public ?string $status = null;

    /**
    * Дата начала отпуска
    * @var Date|null 
    */
    public ?Date $dateFrom = null;

    /**
    * Дата конца отпуска
    * @var Date|null
    */
    public ?Date $dateTo = null;
}
