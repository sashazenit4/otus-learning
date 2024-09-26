<?php

namespace Otus\Components;

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\UserTable;
use enoffspb\BitrixEntityManager\EntityManagerInterface;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Type\Date;
use Bitrix\Main\Error;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\ErrorableImplementation;
use Otus\Vacation\Controller;
use Otus\Vacation\Entity\VacationRequest;
use Otus\Vacation\Entity\VacationRequestApproval;
use Otus\Vacation\Entity\VacationItem;
use Otus\Vacation\Entity\VacationItemApproval;
use Otus\Vacation\AccessManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

\Bitrix\Main\Loader::includeModule('otus.vacation');

class VacationChangeItemComponent extends \CBitrixComponent implements Controllerable, Errorable
{
    use ErrorableImplementation;

    protected EntityManagerInterface $entityManager;

    protected AccessManager $accessManager;

    protected int $userId;

    protected Controller $controller;

    protected string $type;

    public function __construct($component = null)
    {
        global $USER;

        $this->userId = $USER->getId();

        $this->controller = new Controller($this->userId);

        $serviceLocator = ServiceLocator::getInstance();

        $this->entityManager = $serviceLocator->get('otus.vacation.entityManager');
        $this->accessManager = $serviceLocator->get('otus.vacation.accessManager');

        parent::__construct($component);

    }

    public function executeComponent()
    {
        $this->arResult['REQUEST_FIELDS']['requested_user_id'] = $this->userId;
        $this->arResult['TITLE'] = 'Изменение согласованного отпуска';
        $vacationItemInfo = $this->getVacationItems($this->userId);
        $this->arResult['APPROVED_VACATION_ITEMS'] = $vacationItemInfo['items'];
        $this->arResult['APPROVED_VACATION_ITEMS_TYPES'] = $vacationItemInfo['types'];
        $this->arResult['ABSENCE_DATA'] = $this->getAbsenceData();

        $this->IncludeComponentTemplate();
    }

    public function configureActions()
    {
        return [];
    }

    protected function getVacationTypes(): array
    {
        $filter = [
            'PROPERTY_ID' => 'ABSENCE_TYPE',
            'IBLOCK_ID' => Option::get('intranet', 'iblock_absence', 1),
        ];

        $rsVacationTypes = \CIBlockPropertyEnum::getList(['ID' => 'ASC'], $filter);

        $vacationTypes = [];

        $selectedVacationTypes = explode(' ', Option::get('otus.vacation', 'otus_vacation_selected_types'));

        while ($vacationType = $rsVacationTypes->fetch()) {
            if (in_array($vacationType['ID'], $selectedVacationTypes)) {
                $vacationTypes[] = [
                    'id' => $vacationType['ID'],
                    'name' => $vacationType['VALUE'],
                ];
            }
        }

        return $vacationTypes;
    }

    protected function getVacationItems($userId): ?array
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $propertyAbsenceTypeValues = $this->getVacationTypes();

        $propertyAbsenceTypeValuesIds = [];

        foreach ($propertyAbsenceTypeValues as $propValueId) {
            $propertyAbsenceTypeValuesIds[] = $propValueId['id'];
        }

        $arAbsenceDates = \CIBlockElement::getList(
            [],
            [
                'IBLOCK_ID' => Option::get('intranet', 'iblock_absence'),
                'PROPERTY_USER'       => $userId,
                '>ACTIVE_FROM'  => date('d.m.Y'),
                'ACTIVE_TO' => '31.12.' . date('Y'),
                'PROPERTY_ABSENCE_TYPE' => $propertyAbsenceTypeValuesIds,
            ],
            false,
            false,
            ['PROPERTY_ABSENCE_TYPE', 'ID', 'PROPERTY_USER', 'ACTIVE_TO', 'ACTIVE_FROM']
        );

        $vacationItems = [];

        while ($dateInfo = $arAbsenceDates->fetch()) {
            $prop = \CIBlockPropertyEnum::GetByID($dateInfo['PROPERTY_ABSENCE_TYPE_ENUM_ID']);
            $vacationRequestTypeById[$dateInfo['ID']] = $prop['ID'];
            $vacationItems[$dateInfo['ID']] = $dateInfo['ACTIVE_FROM'] . ' - ' . $dateInfo['ACTIVE_TO'];
        }

        return ['items' => $vacationItems, 'types' => $vacationRequestTypeById];
    }

    protected function getAbsenceData(): ?array
    {
        $this->errorCollection = new ErrorCollection();

        $vacationRequestRepository = $this->entityManager->getRepository(VacationRequest::class);

        $arAbsenceDates = \CIntranetUtils::GetAbsenceData(
            [
                'USERS'       => false,
                'DATE_START'  => '01.01.' . date('Y'),
                'DATE_FINISH' => '31.12.' . date('Y'),
                'PER_USER'    => true
            ], \BX_INTRANET_ABSENCE_HR
        );

        $agreedVacationData = [];

        foreach ($arAbsenceDates as $userId => $vacations) {
            $userInfo = \CUser::GetById($userId)->fetch();

            foreach ($vacations as $vacationData) {
                $agreedVacationData[] = [
                    'DATE_FROM' => date('Y-m-d', strtotime($vacationData['DATE_FROM'])),
                    'DATE_TO' => date('Y-m-d', strtotime($vacationData['DATE_TO'])),
                    'FORMATTED_DATE_FROM' => date('d.m.Y', strtotime($vacationData['DATE_FROM'])),
                    'FORMATTED_DATE_TO' => date('d.m.Y', strtotime($vacationData['DATE_TO'])),
                    'ID' => $vacationData['ID'],
                    'USER_ID' => strval($userId),
                    'USER_NAME' => $userInfo['NAME'] . ' ' . $userInfo['LAST_NAME'],
                ];
            }
        }

        $filter = [
            'STATUS' => [
                'REVISION',
                'APPROVAL',
            ],
        ];

        $vacationRequests = $vacationRequestRepository->getList(['filter' => $filter]);

        $vacationRequestIds = [];

        foreach ($vacationRequests as $vacationRequest) {
            $vacationRequestIds[$vacationRequest->id] = $vacationRequest->userId;
        }

        $vacationItemRepository = $this->entityManager->getRepository(VacationItem::class);

        $vacationItemList = $vacationItemRepository->getList(['filter' => ['REQUEST_ID' => array_keys($vacationRequestIds)]]);

        $planVacationRequestData = [];

        foreach ($vacationItemList as $vacationItem) {
            $userId = $vacationRequestIds[$vacationItem->requestId];
            $userInfo = \CUSER::getById($userId)->fetch();

            $planVacationRequestData[] = [
                'DATE_FROM' => $vacationItem->dateFrom->format('Y-m-d'),
                'DATE_TO' => $vacationItem->dateTo->format('Y-m-d'),
                'FORMATTED_DATE_FROM' => $vacationItem->dateFrom->format('d.m.Y'),
                'FORMATTED_DATE_TO' => $vacationItem->dateTo->format('d.m.Y'),
                'ID' => $vacationItem->id,
                'REQUEST_ID' => strval($vacationItem->requestId),
                'USER_ID' => $userId,
                'USER_NAME' => $userInfo['NAME'] . ' ' . $userInfo['LAST_NAME'],
            ];
        }

        return ['vacations' => $agreedVacationData, 'requests' => $planVacationRequestData];
    }
}
