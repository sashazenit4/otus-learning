<?php

namespace Otus\Vacation\Entity;
use Bitrix\Main\Type\DateTime;

/**
* Class VacationRequest
* Сущность Запрос на отпуск
* @package Otus\Vacation\Entity
*/
class VacationRequest
{
    /**
    * Идентификатор запроса 
    * @var int|null
    */
    public ?int $id = null; 

    /**
    * Когда создан запрос
    * @var DateTime|null
    */
    public ?DateTime $createdAt = null; 

    /**
    * Когда обновлен запрос
    * @var DateTime|null
    */
    public ?DateTime $updateAt = null; 

    /**
    * Статус запроса 
    * @var string|null
    */
    public ?string $status = null;
    
    /**
    * Инициатор запроса 
    * @var int|null
    */
    public ?int $initiator = null; 
    
    /**
    * REQUESTED_USER 
    * @var int|null
    */
    public ?int $requestedUser = null;
    
    /**
    * Тип запроса
    * @var string|null
    */
    public ?string $requestType = null;
    
    /**
    * Тип отпуска
    * @var string|null
    */
    public ?string $vacationType = null;
    
    /**
    * Описание запроса
    * @var string|null
    */
    public ?string $description = null;

    /**
     * Является ли запрос запросом на изменение отпуска
     * @var bool|null
     */
    public ?bool $isChangeRequest = null;

    /**
     * Изменяемые отпуска
     * @var string|null
     */
    public ?string $replacedItem = null;
}
