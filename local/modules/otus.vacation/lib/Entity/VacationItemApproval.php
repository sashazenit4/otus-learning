<?php

namespace Otus\Vacation\Entity;

use Bitrix\Main\Type\Date;
use Bitrix\Main\Type\DateTime;

/**
* Class VacationItemApproval
* Сущность Согласование отпуска
* @package Otus\Vacation\Entity
*/
class VacationItemApproval
{
    /**
    * Идентификатор согласования отпуска
    * @var int|null
    */
    public ?int $id = null; 

    /**
    * Когда запущено согласование
    * @var DateTime|null
    */
    public ?DateTime $createdAt = null;

    /**
    * Кто создал
    * @var int|null
    */
    public ?int $createBy = null;

    /**
    * Когда обновлено согласование
    * @var DateTime|null
    */
    public ?DateTime $updateAt = null;
    
    /**
    * Идентификатор отпуска
    * @var int|null
    */
    public ?int $vacationItemId = null;
    
    /**
    * Кто согласует
    * @var int|null
    */
    public ?int $approvalId = null;

    /**
    * Тип согласования отпуска
    * @var string|null
    */
    public ?string $approvalType = null;

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

    /**
    * Описание согласования отпуска
    * @var string|null
    */
    public ?string $description = null;
}
