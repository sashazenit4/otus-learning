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

class VacationFormComponent extends \CBitrixComponent implements Controllerable, Errorable
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
        $currentDate = new Date();

        $this->arResult['IS_NEW'] = false;
        $this->arResult['CURRENT_USER'] = $this->userId;
        $this->arResult['VACATION_TYPE'] = $this->getVacationTypes();
        $this->arResult['CURRENT_DATE'] = $currentDate->format('d.m.Y');

        $this->type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';

        $this->arResult['ABSENCE_DATA'] = $this->getAbsenceData(intval($this->arParams['REQUEST_ID']));

        if (empty($this->arParams['REQUEST_ID'])) {
            if (!$this->accessManager->can($this->userId, 'createVacationRequest')) {
                \showError(Loc::getMessage('NO_ACCESS_ERROR'));
                return;
            }

            $this->arResult['permissions'] = $this->getPermissions();

            $this->arResult["ITEMS"] = [];

            $this->arResult['USER_LIST'] = $this->getUserList();

            $this->arResult['IS_NEW'] = true;
            $this->arResult['TITLE'] = Loc::getMessage('NEW_VACATION_REQUEST_TITLE');
        } else {

            $repository = $this->entityManager->getRepository(VacationRequest::class);
            $vacationRequest = $repository->getById($this->arParams['REQUEST_ID']);

            if (empty($vacationRequest)) {
                \showError(Loc::getMessage('ELEMENT_NOT_FOUND_ERROR'));
                return;
            }

            if (!$this->accessManager->can($this->userId, 'viewVacationRequest', $vacationRequest)) {
                \showError(Loc::getMessage('NO_READ_PERMISSION_ERROR'));
                return;
            }

            $this->arResult['permissions'] = $this->getPermissions($vacationRequest);

            $this->arResult["ITEMS"] = $this->getVacationItems($this->arParams['REQUEST_ID']);
            $this->arResult["VACATION_REQUEST_DESCRIPTION"] = $vacationRequest->description;

            $vacationTypeId = $vacationRequest->vacationType;

            $vacationLabel = current(array_filter($this->arResult['VACATION_TYPE'], static function ($e) use ($vacationTypeId) {
                return $e['id'] == $vacationTypeId;
            }))['name'];

            $this->arResult['REQUEST_FIELDS'] = [
                'description' => $vacationRequest->description,
                'vacation_type_label' => $vacationLabel,
                'requested_user_id' => $vacationRequest->requestedUser

            ];

            $this->arResult['TITLE'] = Loc::getMessage('APPROVAL_VACATION_REQUEST_TITLE');

            $this->arResult['approval_log'] = $this->getVacationRequestApprovalLog($vacationRequest);

            $this->arResult['is_change_request'] = $this->controller->isChangeRequest($vacationRequest);

            if ($this->arResult['is_change_request']) {
                $changedRanges = explode(' ', $vacationRequest->replacedItem);
                $this->arResult['changed_range'] = '';
                foreach ($changedRanges as $key => $val) {
                    $dateRange = $this->getDateRangeById($val);
                    if (!empty($dateRange)) {
                        $this->arResult['changed_range'] .= $key + 1 . ': ' . $dateRange . '<br>';
                    }
                }
            }
        }

        $this->IncludeComponentTemplate();
    }

    public function configureActions()
    {
        return [];
    }

    public function getComponentAction(?int $vacationRequestId = null)
    {
        return new \Bitrix\Main\Engine\Response\Component('otus:vacation.form', '', [
            'REQUEST_ID' => $vacationRequestId
        ]);
    }

    public function saveVacationRequestAction(int $vacationRequestId, array $items = [], array $vacationParams)
    {
        $this->errorCollection = new ErrorCollection();

        $isStrict = Option::get('otus.vacation', 'otus_vacation_strict_checking', 1);

        $isVacationRequestAnnual = false;

        $vacationTypes = $this->getVacationTypes();

        foreach ($vacationTypes as $vacationType) {
            if ($vacationParams['vacationType'] == $vacationType['id'] && $vacationType['name'] == 'отпуск ежегодный') {
                $isVacationRequestAnnual = true;
            }
        }

        if ($isStrict && $isVacationRequestAnnual && empty($vacationParams['replacedItem'])) {
            $checkVacationItems = $this->checkVacationItemsIntersections($items);

            if ($checkVacationItems !== true) {
                foreach ($checkVacationItems as $errorMessage) {
                    $this->errorCollection->setError(new Error($errorMessage));
                }

                return false;
            }
        }

        if ($vacationRequestId == 0) {
            return $this->createVacationRequest($items, $vacationParams);
        } else {
            return $this->editVacationRequest($vacationRequestId, $items, $vacationParams);
        }
    }

    protected function createVacationRequest(array $items = [], array $vacationParams)
    {
        $this->errorCollection = new ErrorCollection();

        $item = [];

        $item["from"] = date("d.m.Y", strtotime($item["from"]));
        $item["to"] = date("d.m.Y", strtotime($item["to"]));

        if (empty($items)) {
            $this->errorCollection->setError(new Error(Loc::getMessage('EMPTY_VACATION_ITEM_LIST_ERROR')));
            return false;
        }

        $dateAr = [];
        foreach ($items as $k => $item) {
            if (!empty($dateAr)) {
                foreach ($dateAr as $dateFromArrItem) {
                    if ((strtotime($item["from"]) <= $dateFromArrItem['TO'] && strtotime($item["from"]) >= $dateFromArrItem['FROM']) || (strtotime($item["to"]) <= $dateFromArrItem['TO'] && strtotime($item["to"]) >= $dateFromArrItem['FROM'])) {
                        $this->errorCollection->setError(new Error(Loc::getMessage('DATES_OVERLAP_ERROR')));
                        return false;
                    }
                }
            }
            $dateAr[$k]["FROM"] = strtotime($item["from"]);
            $dateAr[$k]['TO'] = strtotime($item["to"]);
        }

        $currentDateTime = new DateTime();

        foreach ($items as $k => $item) {
            $dateFrom = Date::createFromTimestamp(strtotime($item["from"]));
            $dateTo = Date::createFromTimestamp(strtotime($item["to"]));
            $items[$k]["dateFrom"] = $dateFrom;
            $items[$k]["dateTo"] = $dateTo;
        }

        $context = [
            'createdAt' => $currentDateTime,
            'updateAt' => $currentDateTime,
            'requestType' => 'PLAN',
            'vacationType' => $vacationParams['vacationType'],
            'initiator' => $this->userId,
            'requestedUser' => $vacationParams['requestedUser'],
            'description' => $vacationParams['description'],
            'status' => 'REVISION',
            'items' => $items,
            'isChangeRequest' => $vacationParams['isChangeRequest'],
            'replacedItem' => $vacationParams['replacedItem'],
        ];

        $result = $this->controller->createVacationRequest($context);

        if ($this->controller->hasErrors()) {
            $this->errorCollection->setError(new Error(Loc::getMessage('NO_CREATE_PERMISSION_ERROR')));
            return false;
        }

        return [
            'vacation_request_id' => $result
        ];

    }

    protected function editVacationRequest(int $vacationRequestId, array $items = [], array $vacationParams)
    {
        $item = [];

        $item["from"] = date("d.m.Y", strtotime($item["from"]));
        $item["to"] = date("d.m.Y", strtotime($item["to"]));

        $this->errorCollection = new ErrorCollection();

        if (empty($items)) {
            $this->errorCollection->setError(new Error(Loc::getMessage('EMPTY_VACATION_ITEM_LIST_ERROR')));
            return false;
        }

        $dateAr = [];
        foreach ($items as $k => $item) {
            if (!empty($dateAr)) {
                foreach ($dateAr as $dateFromArrItem) {
                    if ((strtotime($item["from"]) <= $dateFromArrItem['TO'] && strtotime($item["from"]) >= $dateFromArrItem['FROM']) || (strtotime($item["to"]) <= $dateFromArrItem['TO'] && strtotime($item["to"]) >= $dateFromArrItem['FROM'])) {
                        $this->errorCollection->setError(new Error(Loc::getMessage('DATES_OVERLAP_ERROR')));
                        return false;
                    }
                }
            }
            $dateAr[$k]["FROM"] = strtotime($item["from"]);
            $dateAr[$k]['TO'] = strtotime($item["to"]);
        }

        $currentDateTime = new DateTime();

        foreach ($items as $k => $item) {
            $dateFrom = Date::createFromTimestamp(strtotime($item["from"]));
            $dateTo = Date::createFromTimestamp(strtotime($item["to"]));
            $items[$k]["dateFrom"] = $dateFrom;
            $items[$k]["dateTo"] = $dateTo;
        }
        $context = [
            'id' => $vacationRequestId,
            'updateAt' => $currentDateTime,
            'requestType' => 'PLAN',
            'description' => $vacationParams['description'],
            'status' => 'REVISION',
            'initiator' => $this->userId,
            'items' => $items,
        ];

        $result = $this->controller->editVacationRequest($vacationRequestId, $context);

        if ($this->controller->hasErrors()) {
            $this->errorCollection->setError(new Error(Loc::getMessage('NO_EDIT_PERMISSION_ERROR')));
            return false;
        }

        return [
            'vacation_request_id' => $result
        ];
    }

    protected function getVacationItems(int $vacationRequestId): array
    {
        $repository = $this->entityManager->getRepository(VacationRequestApproval::class);

        $vacationRequestApproval = $repository->getList(['filter' => ['REQUEST_ID' => $vacationRequestId, '!STATUS' => 'COMPLEATED']]);

        $currentApproval = $vacationRequestApproval[0]->approvalId;

        $repository = $this->entityManager->getRepository(VacationItem::class);

        $vacationItemCollection = $repository->getList(['filter' => ['REQUEST_ID' => $vacationRequestId]]);

        $vacationItemIds = [];

        foreach ($vacationItemCollection as $vacationItem) {
            $vacationItemIds[] = $vacationItem->id;
        }

        $filter = [
            'VACATION_ITEM_ID' => $vacationItemIds,
            'APPROVAL_ID' => $currentApproval,
        ];

        $vacationItemApprovalRepository = $this->entityManager->getRepository(VacationItemApproval::class);

        $vacationItemApprovalCollection = $vacationItemApprovalRepository->getList(['filter' => $filter]);

        $vacationItems = [];

        foreach ($vacationItemCollection as $key => $vacationItem) {
            $vacationItemInfoType = null;

            switch ($vacationItemApprovalCollection[$key]->approvalType) {
                case 'PROPOSAL':
                    $vacationItemInfoType = 'warning';
                    break;
                case 'AGREED':
                    $vacationItemInfoType = 'success';
                    break;
                case 'REJECTED':
                    $vacationItemInfoType = 'error';
                    break;
            }

            $vacationItems[] = [
                "id" => $vacationItem->id,
                "from" => $vacationItem->dateFrom->format("Y-m-d"),
                "fromFormatted" => $vacationItem->dateFrom->format("d.m.Y"),
                "to" => $vacationItem->dateTo->format("Y-m-d"),
                "toFormatted" => $vacationItem->dateTo->format("d.m.Y"),
                "info" => [
                    'type' => $vacationItemInfoType,
                    'text' => $vacationItemApprovalCollection[$key]->description,
                ],
                "messages" => [],
            ];

        }

        return $vacationItems;
    }

    public function startVacationRequestApprovalAction(int $vacationRequestId)
    {
        $this->errorCollection = new ErrorCollection();

        $repository = $this->entityManager->getRepository(VacationRequest::class);

        $vacationRequest = $repository->getById($vacationRequestId);

        $currentDateTime = new DateTime();

        $context = [
            'id' => $vacationRequestId,
            'updateAt' => $currentDateTime,
            'requestType' => $vacationRequest->requestType,
            'vacationType' => $vacationRequest->vacationType,
            'initiator' => $vacationRequest->initiator,
            'requestedUser' => $vacationRequest->requestedUser,
            'description' => $vacationRequest->description,
            'status' => $vacationRequest->status,
            'items' => $vacationRequest->items,
            'isChangeRequest' => $vacationRequest->isChangeRequest,
            'replacedItem' => $vacationRequest->replacedItem,
        ];

        $result = $this->controller->startVacationRequestApproval($context);

        if ($this->controller->hasErrors()) {
            $this->errorCollection->setError(new Error(Loc::getMessage('COULD_NOT_START_APPROVAL_ERROR')));
            return false;
        }

        return $result;
    }

    public function rejectVacationRequestAction(int $vacationRequestId, array $items, ?string $rejectComment = null, bool $alternative = false)
    {
        $this->errorCollection = new ErrorCollection();

        $repository = $this->entityManager->getRepository(VacationRequest::class);

        $vacationRequest = $repository->getById($vacationRequestId);

        $currentDateTime = new DateTime();

        $context = [
            'id' => $vacationRequestId,
            'updateAt' => $currentDateTime,
            'requestType' => $vacationRequest->requestType,
            'vacationType' => $vacationRequest->vacationType,
            'initiator' => $vacationRequest->initiator,
            'requestedUser' => $vacationRequest->requestedUser,
            'description' => $vacationRequest->description,
            'status' => $vacationRequest->status,
            'items' => $items,
            'rejectComment' => $rejectComment,
            'alternative' => $alternative,
        ];

        $result = $this->controller->rejectVacationRequest($context);

        if ($this->controller->hasErrors()) {
            $this->errorCollection->setError(new Error(Loc::getMessage('COULD_NOT_REJECT_VACATION_REQUEST_ERROR')));
            return false;
        }

        return $result;
    }

    public function approveVacationRequestAction(int $vacationRequestId, ?string $approveComment = null)
    {
        $this->errorCollection = new ErrorCollection();

        $repository = $this->entityManager->getRepository(VacationRequest::class);

        $vacationRequest = $repository->getById($vacationRequestId);

        $currentDateTime = new DateTime();

        $context = [
            'id' => $vacationRequestId,
            'updateAt' => $currentDateTime,
            'requestType' => $vacationRequest->requestType,
            'vacationType' => $vacationRequest->vacationType,
            'initiator' => $vacationRequest->initiator,
            'requestedUser' => $vacationRequest->requestedUser,
            'description' => $vacationRequest->description,
            'status' => $vacationRequest->status,
            'items' => $vacationRequest->items,
            'approveComment' => $approveComment,
        ];

        $result = $this->controller->approveVacationRequest($context);

        if ($this->controller->hasErrors()) {
            $this->errorCollection->setError(new Error(Loc::getMessage('COULD_NOT_APPROVE_VACATION_REQUEST_ERROR')));
            return false;
        }

        return true;
    }

    public function getPermissions(?VacationRequest $vacationRequest = null)
    {
        $canEdit = false;
        $canApprove = false;
        $canStartApproval = false;

        if (empty($vacationRequest)) {
            $canEdit = $this->accessManager->can($this->userId, 'createVacationRequest');
            $canStartApproval = $canEdit;
        } else {
            $canEdit = $this->accessManager->can($this->userId, 'editVacationRequest', $vacationRequest);
            $canApprove = $this->accessManager->can($this->userId, 'approveVacationRequest', $vacationRequest);
            $canStartApproval = $this->accessManager->can($this->userId, 'startVacationRequestApproval', $vacationRequest);
        }

        return [
            'can_edit' => $canEdit,
            'can_approve' => $canApprove,
            'can_start_approval' => $canStartApproval,
        ];
    }

    protected function getUserList(): array
    {
        $users = [];

        $userRows = UserTable::getList([
            'filter' => [
                'ACTIVE' => true
            ],
            'select' => [
                'ID',
                'NAME',
                'LAST_NAME',
                'LOGIN'
            ]
        ]);

        foreach ($userRows as $userRow) {
            $fullName = implode(' ', array_filter([$userRow['LAST_NAME'], $userRow['NAME']]));

            if (empty($fullName)) {
                $fullName = $userRow['LOGIN'];
            }

            $users[] = [
                'id' => $userRow['ID'],
                'full_name' => $fullName
            ];
        }

        return $users;
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

    protected function getAbsenceData(int $vacationRequestId = 0): ?array
    {
        $this->errorCollection = new ErrorCollection();

        $vacationRequestRepository = $this->entityManager->getRepository(VacationRequest::class);

        $vacationRequest = $vacationRequestRepository->getById($vacationRequestId);

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
            '!ID' => $vacationRequestId,
        ];

        $vacationRequests = $vacationRequestRepository->getList(['filter' => $filter]);

        $vacationRequestIds = [];

        foreach ($vacationRequests as $vacationRequest) {
            $vacationRequestIds[$vacationRequest->id] = $vacationRequest->requestedUser;
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

    protected function getVacationRequestApprovalLog(VacationRequest $vacationRequest): ?array
    {
        $vacationRequestApprovalRepository = $this->entityManager->getRepository(VacationRequestApproval::class);

        $filter = [
            'REQUEST_ID' => $vacationRequest->id,
        ];

        $vacationRequestApprovalCollection = $vacationRequestApprovalRepository->getList(['filter' => $filter]);

        $result = [];

        foreach ($vacationRequestApprovalCollection as $vacationRequestApproval) {
            $userInfo = \CUser::getById($vacationRequestApproval->approvalId)->fetch();

            $avatar = null;

            if (!empty($userInfo["PERSONAL_PHOTO"])) {
                $avatar = \CFile::getPath($userInfo["PERSONAL_PHOTO"]);
            }

            if ($vacationRequestApproval->approvalType !== 'APPROVAL' && !empty($vacationRequestApproval->approvalId)) {
                $result[] = [
                    'APPROVAL_ID' => $vacationRequestApproval->approvalId,
                    'APPROVAL_NAME' => $userInfo['NAME'] . ' ' . $userInfo['LAST_NAME'],
                    'TYPE' => $vacationRequestApproval->approvalType,
                    'DESCRIPTION' => $vacationRequestApproval->description,
                    'COMMENT_TIME' => $vacationRequestApproval->updateAt->format('d.m.Y H:i:s'),
                    'USER_PHOTO' => $avatar,
                ];
            }
        }

        return $result;
    }

    protected function getDateRangeById($elementId): string
    {
        if (!empty($elementId)) {
            $rsRange = \CIBlockElement::getList([], ['ID' => $elementId, 'IBLOCK_ID' => Option::get('intranet', 'iblock_absence', 1)], false, false, ['DATE_ACTIVE_FROM', 'DATE_ACTIVE_TO']);
    
            $range = $rsRange->fetch();

            if (!empty($range)) {
                return $range['DATE_ACTIVE_FROM'] . ' - ' . $range['DATE_ACTIVE_TO'];
            } else {
                return '';
            }

        } else {
            return '';
        }
    }

    protected function checkVacationItemsIntersections(array $items)
    {
        $hasOneLongVacation = false;
        $isDaysSumRelevant = false;
        $daysSum = 0;

        foreach ($items as $item) {
            $dateFromObject = new \DateTime($item['fromFormatted']);
            $dateToObject = new \DateTime($item['toFormatted']);

            $dateDiffObject = $dateFromObject->diff($dateToObject);

            $daysCount = $dateDiffObject->d + 1;
            $daysSum += $daysCount;

            if ($daysCount >= 14) {
                $hasOneLongVacation = true;
            }
        }

        if ($daysSum == 28) {
            $isDaysSumRelevant = true;
        }

        if ($hasOneLongVacation && $isDaysSumRelevant) {
            return true;
        }

        $errorMessage = [];

        if (!$hasOneLongVacation) {
            $errorMessage[] = 'Нет хотя бы одного отпуска 14 дней или более';
        }

        if (!$isDaysSumRelevant) {
            $errorMessage[] = 'Общее количество дней не равно 28';
        }

        return $errorMessage;
    }

    public function deleteVacationRequestByIdAction(int $vacationRequestId)
    {
        $this->errorCollection = new ErrorCollection();

        $result = $this->controller->deleteVacationRequestById($vacationRequestId);

        if ($this->controller->hasErrors()) {
            $this->errorCollection->setError(new Error(Loc::getMessage('COULD_NOT_START_APPROVAL_ERROR')));
            return false;
        }

        return $result;
    }
}
