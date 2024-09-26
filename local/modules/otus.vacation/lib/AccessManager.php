<?php

namespace Otus\Vacation;

use Otus\Vacation\Entity\VacationRequest;
use Otus\Vacation\Entity\VacationRequestApproval;
use Bitrix\Main\DI\ServiceLocator;
use enoffspb\BitrixEntityManager\EntityManagerInterface;

class AccessManager extends AccessManagerBase
{
    protected ServiceLocator $service;

    protected EntityManagerInterface $entityManager;

    public function __construct(array $context)
    {
        $serviceLocator = ServiceLocator::getInstance();

        $this->entityManager = $serviceLocator->get('otus.vacation.entityManager');

        parent::__construct($context);
    }

    /**
     * Получение ролей пользоваеля
     * @param int $userId
     * @return UserRoles
     */
    public function getUserRoles(int $userId): UserRoles
    {
        return new UserRoles($userId, $this->roleGroupMap, $this->roleUserMap);
    }

    protected function canViewVacationList(int $userId): bool
    {
        $userRoles = $this->getUserRoles($userId);

        return $userRoles->has(UserRoles::ROLE_EMPLOYEE) || $userRoles->has(UserRoles::ROLE_MODERATOR);
    }

    protected function canCreateVacationRequest(int $userId): bool
    {
        $userRoles = $this->getUserRoles($userId);

        return $userRoles->has(UserRoles::ROLE_EMPLOYEE);
    }

    protected function canViewVacationRequest(int $userId, VacationRequest $vacationRequest): bool
    {
        $userRoles = $this->getUserRoles($userId);
        return $this->isUserApprover($userId, $vacationRequest->id) || $vacationRequest->initiator == $userId && $userRoles->has(UserRoles::ROLE_EMPLOYEE) || $userRoles->has(UserRoles::ROLE_MODERATOR) || $userRoles->has(UserRoles::ROLE_ACCOUNTANT);
    }

    protected function canEditVacationRequest(int $userId, ?VacationRequest $vacationRequest = null): bool
    {
        if (empty($vacationRequest)) {
            $userRoles = $this->getUserRoles($userId);

            return $userRoles->has(UserRoles::ROLE_EMPLOYEE);
        }

        return $vacationRequest->initiator == $userId && $vacationRequest->status == 'REVISION';
    }

    protected function canStartVacationRequestApproval(int $userId, VacationRequest $vacationRequest): bool
    {
        return $vacationRequest->initiator == $userId && $vacationRequest->status == 'REVISION';
    }

    protected function canApproveVacationRequest(int $userId, VacationRequest $vacationRequest): bool
    {
        return $this->isUserApprover($userId, $vacationRequest->id, 'PROCESS') && $vacationRequest->status == 'APPROVAL';
    }

    protected function canRejectVacationRequest(int $userId, VacationRequest $vacationRequest): bool
    {
        return $this->isUserApprover($userId, $vacationRequest->id, 'PROCESS') && $vacationRequest->status == 'APPROVAL';
    }

    protected function canChangeVacationItem(int $userId, VacationRequest $vacationRequest): bool
    {
        return $vacationRequest->initiator == $userId;
    }

    /**
     * Является ли пользователь согласующим
     * @param int $userId
     * @param int $verificationRequestId
     * @param string|null $status
     * @return bool
     */
    protected function isUserApprover(int $userId, int $verificationRequestId, ?string $status = null): bool
    {
        $filter = [
            'REQUEST_ID' => $verificationRequestId,
            'APPROVAL_ID' => $userId,
        ];

        if(!empty($status)) {
            $filter['STATUS'] = $status;
        }

        $vacationRequestApprovalCollection = $this->entityManager->getRepository(VacationRequestApproval::class)
            ->getList(['filter' => $filter]);

        return !empty($vacationRequestApprovalCollection);
    }

    public function isUserAccountant(int $userId): bool
    {
        if ($this->getUserRoles($userId)->has(UserRoles::ROLE_ACCOUNTANT)) {
            return true;
        }

        return false;        
    }

    protected function canDeleteVacationRequest(int $userId, VacationRequest $vacationRequest)
    {
        if (($vacationRequest->requestedUser == $userId || $vacationRequest->initiator == $userId) &&  ($vacationRequest->status == 'REVISION' || $vacationRequest->status == 'RETURNED')) {
            return true;
        }

        return false;
    }
}
