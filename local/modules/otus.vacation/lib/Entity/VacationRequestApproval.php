<?php

namespace Otus\Vacation\Entity;
use Bitrix\Main\Type\DateTime;

/**
* Class VacationRequestApproval
* Сущность Согласование запроса на отпуск
* @package Otus\Vacation\Entity
*/
class VacationRequestApproval
{
    /**
    * Идентификатор согласования запроса
    * @var int|null
    */
    public ?int $id = null; 

    /**
    * Когда запущено согласование запроса
    * @var DateTime|null
    */
    public ?DateTime $createdAt = null;

    /**
    * Когда обновлено согласование запроса
    * @var DateTime|null
    */
    public ?DateTime $updateAt = null;

    /**
    * Идентификатор запроса
    * @var int|null
    */
    public ?int $requestId = null;

    /**
    * Кто согласует
    * @var int|null
    */
    public ?int $approvalId = null;

    /**
    * Статус согласования запроса
    * @var string|null
    */
    public ?string $status = null;
    
    /**
    * Тип согласования запроса
    * @var string|null
    */
    public ?string $approvalType = null;
    
    /**
    * Описание согласования запроса
    * @var string|null
    */
    public ?string $description = null; 
}
