<?php

namespace Otus\Vacation;

use Bitrix\Main\DB\MysqlCommonConnection;
use enoffspb\BitrixEntityManager\EntityManagerInterface;
use Otus\Vacation\Entity\VacationRequest;
use Otus\Vacation\Entity\VacationItem;
use Otus\Vacation\Entity\VacationRequestApproval;
use Otus\Vacation\Entity\VacationItemApproval;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Type\Date;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use CIBlockElement;

Loc::loadMessages(__FILE__);

class Service
{
    protected EntityManagerInterface $entityManager;

    protected MysqlCommonConnection $connection;

    protected array $errors = [];

    public function __construct(EntityManagerInterface $entityManager, MysqlCommonConnection $connection)
    {
        $this->entityManager = $entityManager;
        $this->connection = $connection;
    }

    /**
     * Получение массива ошибок
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Проверка на наличие ошибок
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !empty($this->getErrors());
    }

    public function createVacationRequestFromContext(array $context): ?int
    {
        $vacationRequest = new VacationRequest();

        $vacationRequest->createdAt = $context['createdAt'];
        $vacationRequest->updateAt = $context['updateAt'];
        $vacationRequest->requestType = $context['requestType'];
        $vacationRequest->vacationType = $context['vacationType'];
        $vacationRequest->initiator = $context['initiator'];
        $vacationRequest->requestedUser = $context['requestedUser'];
        $vacationRequest->description = $context['description'];
        $vacationRequest->status = $context['status'];
        $vacationRequest->isChangeRequest = $context['isChangeRequest'] == true ? 1 : 0;
        $vacationRequest->replacedItem = $context['replacedItem'];

        if (!$this->entityManager->save($vacationRequest)) {
            $this->errors[] = Loc::getMessage('SAVE_ERROR');
            return null;
        }

        foreach ($context['items'] as $item) {
            $vacationItem = new VacationItem();

            $vacationItem->createdAt = $context['createdAt'];
            $vacationItem->updateAt = $context['updateAt'];
            $vacationItem->requestId = $vacationRequest->id;
            $vacationItem->status = $item['status'];
            $vacationItem->dateFrom = $item['dateFrom'];
            $vacationItem->dateTo = $item['dateTo'];

            if (!$this->entityManager->save($vacationItem)) {
                $this->errors[] = Loc::getMessage('SAVE_ERROR');
                return null;
            }
        }

        return $vacationRequest->id;
    }

    public function editVacationRequestFromContext(int $vacationRequestId, array $context): ?int
    {
        $vacationRequestRepository = $this->entityManager->getRepository(VacationRequest::class);
        $vacationRequest = $vacationRequestRepository->getById($vacationRequestId);

        $vacationRequest->updateAt = $context['updateAt'];
        $vacationRequest->description = $context['description'];

        $vacationItemRepository = $this->entityManager->getRepository(VacationItem::class);
        $vacationItems = $vacationItemRepository->getList(['filter' => ['REQUEST_ID' => $vacationRequestId]]);

        $contextIds = [];

        foreach ($context['items'] as $item) {
            if ($item["id"] == 0) {
                $vacationItem = new VacationItem();

                $vacationItem->createdAt = $context['createdAt'];
                $vacationItem->updateAt = $context['updateAt'];
                $vacationItem->requestId = $vacationRequest->id;
                $vacationItem->status = $item['status'];
                $vacationItem->dateFrom = $item['dateFrom'];
                $vacationItem->dateTo = $item['dateTo'];

                if (!$this->entityManager->save($vacationItem)) {
                    $this->errors[] = Loc::getMessage('SAVE_ERROR');
                    return null;
                }
            } else {
                foreach ($vacationItems as $existedItem) {
                    if ($existedItem->id == $item["id"]) {
                        $existedItem->updateAt = $context["updateAt"];
                        $existedItem->requestId = $vacationRequestId;
                        $existedItem->dateFrom = $item["dateFrom"];
                        $existedItem->dateTo = $item["dateTo"];
                        if (!$this->entityManager->update($existedItem)) {
                            $this->errors[] = Loc::getMessage('SAVE_ERROR');
                            return null;
                        }
                    }
                }
            }
            $contextIds[] = $item["id"];
        }

        foreach ($vacationItems as $existedItem) {
            if (!in_array($existedItem->id, $contextIds)) {
                if (!$this->entityManager->delete($existedItem)) {
                    $this->errors[] = Loc::getMessage('DELETE_ERROR');
                    return null;
                }
            }
        }

        if (!$this->entityManager->update($vacationRequest)) {
            $this->errors[] = Loc::getMessage('UPDATE_ERROR');
            return null;
        }

        return $vacationRequest->id;
    }

    public function startVacationRequestApprovalFromContext(array $context): bool
    {
        $this->connection->startTransaction();

        $filter = [
            'REQUEST_ID' => $context['id'],
        ];

        $vacacationItemRepository = $this->entityManager->getRepository(VacationItem::class);

        $vacationItemCollection = $vacacationItemRepository->getList(['filter' => $filter]);

        $vacationItemIds = [];

        foreach ($vacationItemCollection as $vacationItem) {
            $vacationItemIds[] = $vacationItem->id;
        }

        $filter = [
            'VACATION_ITEM_ID' => $vacationItemIds,
        ];

        $vacationItemApprovalRepository = $this->entityManager->getRepository(VacationItemApproval::class);

        $vacationItemApprovalCollection = $vacationItemApprovalRepository->getList(['filter' => $filter]);

        foreach ($vacationItemApprovalCollection as $vacationItemApproval) {
            if (!$this->entityManager->delete($vacationItemApproval)) {
                $this->errors[] = Loc::getMessage('SAVE_APPROVAL_CHAIN_ERROR');
                $this->connection->rollbackTransaction();
                return false;
            }
        }

        $approvers = $this->getApproversChain($context);

        foreach ($approvers as $key => $approver) {

            $vacationRequestApproval = new VacationRequestApproval();

            $vacationRequestApproval->createdAt = $context['updateAt'];
            $vacationRequestApproval->updateAt = $context['updateAt'];
            $vacationRequestApproval->requestId = $context['id'];
            $vacationRequestApproval->approvalId = $approver;
            $vacationRequestApproval->approvalType = 'APPROVAL';

            if ($key == 0) {
                $vacationRequestApproval->status = 'PROCESS';
            } else {
                $vacationRequestApproval->status = 'WAITING';
            }

            if (!$this->entityManager->save($vacationRequestApproval)) {
                $this->errors[] = Loc::getMessage('SAVE_APPROVAL_CHAIN_ERROR');
                $this->connection->rollbackTransaction();
                return false;
            }
        }

        $vacationRequest = $this->entityManager->getRepository(VacationRequest::class)->getById($context['id']);
        $vacationRequest->status = 'APPROVAL';
        $vacationRequest->updateAt = $context['updateAt'];

        if (!$this->entityManager->update($vacationRequest)) {
            $this->errors[] = Loc::getMessage('SAVE_APPROVAL_CHAIN_ERROR');
            $this->connection->rollbackTransaction();
            return false;
        }

        $vacationItemCollection = $this->entityManager->getRepository(VacationItem::class)->getList(['filter' => [
            'REQUEST_ID' => $context['id'],
        ]]);

        $currentDateTime = new DateTime();

        foreach ($vacationItemCollection as $vacationItem) {
            foreach ($approvers as $approver) {
                $vacationItemApproval = new VacationItemApproval();

                $vacationItemApproval->createdAt = $currentDateTime;
                $vacationItemApproval->createBy = $context['initiator'];
                $vacationItemApproval->updateAt = $currentDateTime;
                $vacationItemApproval->vacationItemId = $vacationItem->id;
                $vacationItemApproval->approvalId = $approver;

                if (!$this->entityManager->save($vacationItemApproval)) {
                    $this->errors[] = Loc::getMessage('CREATE_APPROVAL_CHAIN_ERROR');
                    $this->connection->rollbackTransaction();
                    return false;
                }
            }
        }

        $this->connection->commitTransaction();

        $userInfo = \CUser::getByID($context['requestedUser'])->fetch();

        $userFullName = $userInfo['NAME'] . ' ' . $userInfo['LAST_NAME'];

        $this->sendNotify($context['id'], $approvers[0], 'Вам пришёл <a href="/vacation_request/' . $context['id'] . '/">отпуск</a> от ' . $userFullName . ' на согласование');

        return true;
    }

    public function rejectVacationRequestFromContext(array $context): bool
    {
        $filter = [
            'REQUEST_ID' => $context['id'],
        ];

        $this->connection->startTransaction();

        $vacationRequestApprovalCollection = $this->entityManager->getRepository(VacationRequestApproval::class)->getList(['filter' => $filter]);

        if (!$context['alternative']) {

            foreach ($vacationRequestApprovalCollection as $vacationRequestApproval) {
                if ($vacationRequestApproval->status == 'WAITING' || $vacationRequestApproval->status == 'RETURNED') {
                    if (!$this->entityManager->delete($vacationRequestApproval)) {
                        $this->errors[] = Loc::getMessage('DELETE_APPROVAL_CHAIN_ERROR');
                        $this->connection->rollbackTransaction();
                        return false;
                    }
                } elseif ($vacationRequestApproval->status == 'PROCESS') {
                    $approvalId = $vacationRequestApproval->approvalId;
                    $vacationRequestApproval->description = $context['rejectComment'];
                    $vacationRequestApproval->updateAt = $context['updateAt'];
                    $vacationRequestApproval->approvalType = 'REJECTED';

                    if (!$this->entityManager->update($vacationRequestApproval)) {
                        $this->errors[] = Loc::getMessage('UPDATE_APPROVAL_CHAIN_ERROR');
                        $this->connection->rollbackTransaction();
                        return false;
                    }
                }
            }

            $vacationRequest = $this->entityManager->getRepository(VacationRequest::class)->getById($context['id']);
            $vacationRequest->status = 'REJECTED';
            $vacationRequest->updateAt = $context['updateAt'];

            if (!$this->entityManager->update($vacationRequest)) {
                $this->errors[] = Loc::getMessage('DELETE_APPROVAL_CHAIN_ERROR');
                $this->connection->rollbackTransaction();
                return false;
            }

        } else {

            foreach ($vacationRequestApprovalCollection as $vacationRequestApproval) {
                if ($vacationRequestApproval->status == 'PROCESS' || $vacationRequestApproval->status == 'WAITING') {
                    $vacationRequestApproval->description = $context['rejectComment'];
                    if ($vacationRequestApproval->status == 'PROCESS') {
                        $vacationRequestApproval->approvalType = 'REVISION';
                    }

                    if ($vacationRequestApproval->status == 'PROCESS') {
                        $vacationRequestApproval->updateAt = $context['updateAt'];
                        $approvalId = $vacationRequestApproval->approvalId;
                    }

                    $vacationRequestApproval->status = 'RETURNED';

                    if (!$this->entityManager->update($vacationRequestApproval)) {
                        $this->errors[] = Loc::getMessage('UPDATE_APPROVAL_CHAIN_ERROR');
                        $this->connection->rollbackTransaction();

                        return false;
                    }
                } else {
                    $vacationRequestApproval->updateAt = $context['updateAt'];

                    if (!$this->entityManager->update($vacationRequestApproval)) {
                        $this->errors[] = Loc::getMessage('UPDATE_APPROVAL_CHAIN_ERROR');
                        $this->connection->rollbackTransaction();

                        return false;
                    }
                }
            }

            $vacationRequest = $this->entityManager->getRepository(VacationRequest::class)->getById($context['id']);

            $vacationRequest->status = 'REVISION';
            $vacationRequest->updateAt = $context['updateAt'];

            if (!$this->entityManager->update($vacationRequest)) {
                $this->errors[] = Loc::getMessage('DELETE_APPROVAL_CHAIN_ERROR');
                $this->connection->rollbackTransaction();

                return false;
            }

        }

        $repository = $this->entityManager->getRepository(VacationItem::class);

        $vacationItemCollection = $repository->getList(['filter' => ['REQUEST_ID' => $context['id']]]);

        $vacationItemIds = [];

        foreach ($vacationItemCollection as $vacationItem) {
            $vacationItemIds[] = $vacationItem->id;
        }

        $vacationItemApprovalRepository = $this->entityManager->getRepository(VacationItemApproval::class);

        $vacationItemApprovalCollection = $vacationItemApprovalRepository->getList([
            'filter' => [
                'VACATION_ITEM_ID' => $vacationItemIds,
                'APPROVAL_ID' => $approvalId,
            ],
            'order' => [
                'ID' => 'ASC',
            ],
        ]);

        foreach ($vacationItemApprovalCollection as $key => $vacationItemApproval) {

            $vacationItemApprovalComment = $context['items'][$key]['info']['text'];

            $vacationItemApprovalDateFrom = $context['items'][$key]['info']['dateFrom'];
            $vacationItemApprovalDateTo = $context['items'][$key]['info']['dateTo'];

            if (!empty($vacationItemApprovalComment)) {
                $vacationItemApproval->description = $vacationItemApprovalComment;
                switch ($context['items'][$key]['info']['type']) {
                    case 'warning':
                        $vacationItemApproval->approvalType = 'PROPOSAL';
                        break;
                    case 'success':
                        $vacationItemApproval->approvalType = 'AGREED';
                        break;
                    case 'error':
                        $vacationItemApproval->approvalType = 'REJECTED';
                        break;
                    default:
                        break;
                }

                if (!empty($vacationItemApprovalDateFrom) && !empty($vacationItemApprovalDateTo)) {
                    $dateFrom = Date::createFromTimestamp(strtotime($vacationItemApprovalDateFrom));
                    $dateTo = Date::createFromTimestamp(strtotime($vacationItemApprovalDateTo));

                    $vacationItemApproval->dateFrom = $dateFrom;
                    $vacationItemApproval->dateTo = $dateTo;
                }

                $vacationItemApproval->updateAt = $context['updateAt'];

                if (!$this->entityManager->update($vacationItemApproval)) {
                    $this->errors[] = Loc::getMessage('UPDATE_APPROVAL_CHAIN_ERROR');
                    $this->connection->rollbackTransaction();
                    return false;
                }
            }
        }

        if (!$context['altenative']) {
            $this->sendNotify($context['id'], $context['initiator'], 'Отпуск отклонён. Комментарий: ' . $context['rejectComment']);
        } else {
            $this->sendNotify($context['id'], $context['initiator'], 'Отпуск отклонён. Предложены новые даты. Комментарий: ' . $context['rejectComment']);
        }

        $this->connection->commitTransaction();

        return true;
    }

    public function approveVacationRequestFromContext(array $context): bool
    {
        $filter = [
            'REQUEST_ID' => $context['id'],
            'STATUS' => 'PROCESS',
        ];

        $this->connection->startTransaction();

        $vacationRequestApprovalCollection = $this->entityManager->getRepository(VacationRequestApproval::class)->getList(['filter' => $filter]);

        $currentVacationRequestApproval = $vacationRequestApprovalCollection[0];

        $currentVacationRequestApproval->status = 'COMPLEATED';
        $currentVacationRequestApproval->approvalType = 'AGREED';
        $currentVacationRequestApproval->description = $context['approveComment'];
        $currentVacationRequestApproval->updateAt = $context['updateAt'];

        if (!$this->entityManager->update($currentVacationRequestApproval)) {
            $this->errors[] = Loc::getMessage('UPDATE_APPROVAL_CHAIN_ERROR');
            $this->connection->rollbackTransaction();
            return false;
        }

        $nextVacationRequestApproval = ($this->entityManager->getRepository(VacationRequestApproval::class)->getList([
            'filter' => [
                "REQUEST_ID" => $context['id'],
                "STATUS" => "WAITING",
            ],
            'select' => [
                "ID",
                "APPROVAL_ID",
            ],
            'order' => [
                'ID' => "ASC",
            ],
        ]))[0];

        if (empty($nextVacationRequestApproval)) {
            if (!$this->completeVacationRequestFromContext($context)) {
                $this->errors[] = Loc::getMessage('COMPLETE_APPROVAL_CHAIN_ERROR');
                return false;
            }
        } else {
            $nextVacationRequestApproval->status = 'PROCESS';
            $nextVacationRequestApproval->updateAt = $context['updateAt'];

            if (!$this->entityManager->update($nextVacationRequestApproval)) {
                $this->errors[] = Loc::getMessage('UPDATE_APPROVAL_CHAIN_ERROR');
                $this->connection->rollbackTransaction();

                return false;
            }

            $userInfo = \CUser::getByID($context['requestedUser'])->fetch();

            $userFullName = $userInfo['NAME'] . ' ' . $userInfo['LAST_NAME'] . ' ' . $userInfo['SECOND_NAME'];

            $this->sendNotify($context['id'], $nextVacationRequestApproval->approvalId, 'Вам пришёл <a href="/vacation_request/' . $context['id'] . '/">отпуск</a> от ' . $userFullName . ' на согласование');

            $this->connection->commitTransaction();
        }

        return true;
    }

    public function completeVacationRequestFromContext(array $context): bool
    {
        $vacationRequestRepository = $this->entityManager->getRepository(VacationRequest::class);
        $vacationRequest = $vacationRequestRepository->getById($context['id']);

        $vacationRequest->status = 'AGREED';
        $vacationRequest->updateAt = $context['updateAt'];

        if (!$this->entityManager->update($vacationRequest)) {
            $this->errors[] = Loc::getMessage('COMPLETE_APPROVAL_CHAIN_ERROR');
            $this->connection->rollbackTransaction();
            return false;
        }

        if ($vacationRequest->isChangeRequest) {
            $replacedItemsArray = explode(' ', $vacationRequest->replacedItem);

            $model = new CIBlockElement;

            foreach ($replacedItemsArray as $absenceItemToDeleteId) {
                $model->delete($absenceItemToDeleteId);
            }
        }

        $filter = [
            'REQUEST_ID' => $context['id'],
        ];

        $absenceIblockId = Option::get('intranet', 'iblock_absence', 1);

        $vacationItemCollection = $this->entityManager->getRepository(VacationItem::class)->getList(['filter' => $filter]);

        $vacationTypes = $this->getVacationTypes();

        foreach ($vacationItemCollection as $vacationItem) {
            $rsElement = new \CIBlockElement;

            $arFields = [
                "ACTIVE" => "Y",
                "NAME" => "Otus: " . $vacationTypes[$vacationRequest->vacationType],
                'IBLOCK_ID' => $absenceIblockId,
                'ACTIVE_FROM' => $vacationItem->dateFrom,
                'ACTIVE_TO' => $vacationItem->dateTo,
                "PROPERTY_VALUES" => [
                    'USER' => $vacationRequest->requestedUser,
                    'ABSENCE_TYPE' => $vacationRequest->vacationType,
                ]
            ];

            if (!$elementId = $rsElement->Add($arFields)) {
                $this->errors[] = $rsElement->LAST_ERROR;
                $this->connection->rollbackTransaction();
                return false;
            }
        }

        $this->connection->commitTransaction();

        $this->sendNotify($context['id'], $context['initiator'], 'Ваш <a href="/vacation_request/' . $context['id'] . '/">отпуск</a> согласован');

        return true;
    }

    /**
     * @param $vacationRequestId
     * @param $userId
     * @param $aliasToComponent
     * @return false|void
     */
    protected function sendNotify($vacationRequestId, $userId, $text = '', $aliasToComponent = '123')
    {

        if (IsModuleInstalled("im") && \CModule::IncludeModule("im")) {
            $arMessageFields = array(
                "TO_USER_ID" => $userId,
                "FROM_USER_ID" => 0,
                "NOTIFY_TYPE" => IM_NOTIFY_SYSTEM,
                "NOTIFY_MODULE" => "otus.vacation",
                "NOTIFY_MESSAGE" => $text,
                "NOTIFY_MESSAGE_OUT" => $text,
            );
            $res_mes = \CIMNotify::Add($arMessageFields);

            if (!$res_mes) {
                $this->errors[] = Loc::getMessage('SEND_NOTIFY_ERROR');
                return false;
            }
        }
    }

    protected function getApproversChain(?array $context): ?array
    {
        Loader::includeModule('intranet');
        Loader::includeModule('iblock');

        $structureIblockId = Option::get('intranet', 'iblock_structure', 3);

        $currentUser = isset($context['requestedUser']) ? $context['requestedUser'] : $context['initiator'];

        $userDepartment = \CIntranetUtils::GetUserDepartments($currentUser)[0];

        $rsSections = \CIBlockSection::getNavChain($structureIblockId, $userDepartment);

        $approvers = [];
        while ($arSection = $rsSections->Fetch()) {
            $currentDepartment = [$arSection['ID']];
            $approvers = array_merge($approvers, array_keys(\CIntranetUtils::GetDepartmentManager($currentDepartment, $currentUser, true)));
        }

        $accountantId = Option::get('otus.vacation', 'otus_vacation_account_id', 1);

        $approvers = array_unique($approvers);

        if (empty($approvers)) {
            $approvers = [$accountantId];
        } else {
            $approvers = array_reverse($approvers);
            $headId = $approvers[count($approvers) - 1];

            $approvers[count($approvers) - 1] = $accountantId;
            array_push($approvers, $headId);
        }

        return $approvers;
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
                $vacationTypes[$vacationType['ID']] = $vacationType['VALUE'];
            }
        }

        return $vacationTypes;
    }

    public function deleteVacationRequestById(int $vacationRequestId): bool
    {
        $this->connection->startTransaction();

        $vacationRequestRepository = $this->entityManager->getRepository(VacationRequest::class);
        $vacationRequest = $vacationRequestRepository->getById($vacationRequestId);

        if (!in_array($vacationRequest->status, ['REVISION', 'RETURNED'])) {
            $this->connection->rollBackTransaction();
            $this->errors[] = 'Нельзя удалить заявку';
            return false;
        }

        $vacationRequestApprovalRepository = $this->entityManager->getRepository(VacationRequestApproval::class);
        $vacationRequestApprovalCollection = $vacationRequestApprovalRepository->getList(['filter' => ['REQUEST_ID' => $vacationRequestId]]);

        $vacationItemRepository = $this->entityManager->getRepository(VacationItem::class);
        $vacationItemCollection = $vacationItemRepository->getList(['filter' => ['REQUEST_ID' => $vacationRequestId]]);

        $vacacationItemApprovalCollection = [];

        foreach ($vacationItemCollection as $vacationItem) {
            $vacationItemApprovalRepository = $this->entityManager->getRepository(VacationItemApproval::class);
            $vacacationItemApprovalCollection[] = array_merge(
                $vacacationItemApprovalCollection,
                $vacationItemApprovalRepository->getList(['filter' => ['VACATION_ITEM_ID' => $vacationItem->id]])
            );
        }

        if (!$this->entityManager->delete($vacationRequest)) {
            $this->errors[] = 'Не удалось удалить заявку';
            $this->connection->rollbackTransaction();
            return false;
        }


        foreach ($vacationRequestApprovalCollection as $vacationRequestApproval) {
            if (!$this->entityManager->delete($vacationRequestApproval)) {
                $this->errors[] = 'Не удалось удалить заявку';
                $this->connection->rollbackTransaction();
                return false;
            }
        }

        foreach ($vacationItemCollection as $vacationItem) {
            if (!$this->entityManager->delete($vacationItem)) {
                $this->errors[] = 'Не удалось удалить заявку';
                $this->connection->rollbackTransaction();
                return false;
            }
        }

        foreach ($vacationItemApprovalRepository as $vacationItemApproval) {
            if (!$this->entityManager->delete($vacationItemApproval)) {
                $this->errors[] = 'Не удалось удалить заявку';
                $this->connection->rollbackTransaction();
                return false;
            }
        }

        $this->connection->commitTransaction();

        return true;
    }
}
