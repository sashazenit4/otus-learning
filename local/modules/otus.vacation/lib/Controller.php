<?php

namespace Otus\Vacation;

use Bitrix\Main\DI\ServiceLocator;
use \Bitrix\Main\Localization\Loc;
use Otus\Vacation\Entity\VacationRequest;
use enoffspb\BitrixEntityManager\EntityManagerInterface;
use Otus\Vacation\Entity\VacationRequestApproval;

Loc::loadMessages(__FILE__);
class Controller
{

    protected int $userId;

    /**
     * Менеджер доступа
     * @var AccessManager
     */
    protected AccessManager $accessManager;

    /**
     * Сервис слой
     * @var Service
     */
    protected Service $service;

    protected array $errors = [];

    /**
     * Менеджер сущнойстей
     * @var EntityManager
     */
    protected EntityManagerInterface $entityManager;

    public function __construct(int $userId = 0)
    {
        $this->userId = $userId;

        $serviceLocator = ServiceLocator::getInstance();

        $this->service = $serviceLocator->get('otus.vacation.service');
        $this->accessManager = $serviceLocator->get('otus.vacation.accessManager');
        $this->entityManager = $serviceLocator->get('otus.vacation.entityManager');
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

    public function createVacationRequest(array $context): ?int
    {

        if (!$this->accessManager->can($context['initiator'], 'createVacationRequest')) {
            $this->errors[] = Loc::getMessage("BAD_PERMISSION");;
            return null;
        }

        $requestId = $this->service->createVacationRequestFromContext($context);

        if ($this->service->hasErrors()) {
            $this->errors = array_merge($this->getErrors(), $this->service->getErrors());
        }

        return $requestId;
    }

    public function editVacationRequest(int $vacationRequestId, array $context): ?int
    {
        $repository = $this->entityManager->getRepository(VacationRequest::class);

        $vacationRequest = $repository->getById($context['id']);

        if (!$this->accessManager->can($context['initiator'], 'editVacationRequest', $vacationRequest)) {
            $this->errors[] = Loc::getMessage("BAD_PERMISSION");
            return null;
        }

        $requestId = $this->service->editVacationRequestFromContext($vacationRequestId, $context);

        if ($this->service->hasErrors()) {
            $this->errors = array_merge($this->getErrors(), $this->service->getErrors());
        }

        return $requestId;
    }

    public function getVacationRequestCollection(array $params = []): ?array
    {
        if (!$this->accessManager->can($this->userId, 'viewVacationList')) {
            $this->errors[] = Loc::getMessage("BAD_PERMISSION");
            return null;
        }

        $roleFilter = $this->getVacationRequestCollectionFilterByUser();

        if ($roleFilter === false) {
            $roleFilter = [
                'ID' => false,
            ];
        }

        $params["filter"] = array_merge($params["filter"], $roleFilter);

        $vacationRequestRepository = $this->entityManager->getRepository(VacationRequest::class);
        return $vacationRequestRepository->getList($params);
    }

    public function startVacationRequestApproval(array $context): bool
    {
        $repository = $this->entityManager->getRepository(VacationRequest::class);
        $vacationRequest = $repository->getById($context['id']);

        if (!$this->accessManager->can($this->userId, 'startVacationRequestApproval', $vacationRequest)) {
            $this->errors[] = Loc::getMessage("BAD_PERMISSION");
            return false;
        }

        $result = $this->service->startVacationRequestApprovalFromContext($context);

        if ($this->service->hasErrors()) {
            $this->errors[] = Loc::getMessage('COULD_NOT_INITIALIZE_APPROVAL_CHAIN');
            return false;
        }

        return $result;
    }

    public function rejectVacationRequest(array $context): bool
    {
        $repository = $this->entityManager->getRepository(VacationRequest::class);

        $vacationRequest = $repository->getById($context['id']);

        if (!$this->accessManager->can($this->userId, 'rejectVacationRequest', $vacationRequest)) {
            $this->errors[] = Loc::getMessage("BAD_PERMISSION");
            return false;
        }

        $result = $this->service->rejectVacationRequestFromContext($context);

        if ($this->service->hasErrors()) {
            $this->errors[] = Loc::getMessage('COULD_NOT_REJECT_VACATION_REQUEST');
            return false;
        }

        return $result;
    }

    public function approveVacationRequest(array $context): bool
    {
        $repository = $this->entityManager->getRepository(VacationRequest::class);

        $vacationRequest = $repository->getById($context['id']);
        
        if (!$this->accessManager->can($this->userId, 'approveVacationRequest', $vacationRequest)) {
            $this->errors[] = Loc::getMessage("BAD_PERMISSION");
            return false;
        }

        $result = $this->service->approveVacationRequestFromContext($context);

        if ($this->service->hasErrors()) {
            $this->errors[] = Loc::getMessage('COULD_NOT_APPROVE_VACATION_REQUEST');
            return false;
        }

        return $result;
    }

    private function getVacationRequestCollectionFilterByUser()
    {
        if ($this->accessManager->isUserAccountant($this->userId)) {
            return [];
        } 

        $vacationRequestWhereUserIsApproval = $this->getVacationRequestCollectionWhereUserIsApprovalOrInitiator();

        if (!empty($vacationRequestWhereUserIsApproval)) {
            return [
                'ID' => $vacationRequestWhereUserIsApproval,
            ];
        }

        return false;
    }

    private function getVacationRequestCollectionWhereUserIsApprovalOrInitiator(): ?array
    {
        $vacationRequestApprovalRepository = $this->entityManager->getRepository(VacationRequestApproval::class);

        $filter = [
            'APPROVAL_ID' => $this->userId,
        ];

        $vacationRequestApprovalCollection = $vacationRequestApprovalRepository->getList(['filter' => $filter]);

        $vacationRequestIds = [];

        foreach ($vacationRequestApprovalCollection as $vacationRequestApproval) {
            $vacationRequestIds[] = $vacationRequestApproval->requestId;
        }

        $vacationRequestRepository = $this->entityManager->getRepository(VacationRequest::class);

        $filter = [
            'INITIATOR' => $this->userId,
        ];

        $vacationRequestCollection = $vacationRequestRepository->getList(['filter' => $filter]);

        foreach ($vacationRequestCollection as $vacationRequest) {
            $vacationRequestIds[] = $vacationRequest->id;           
        }

        return $vacationRequestIds;
    }

    public function isChangeRequest($vacationRequest)
    {
        return $vacationRequest->isChangeRequest;
    }

    public function deleteVacationRequestById(int $vacationRequestId): bool
    {   
        $result = $this->service->deleteVacationRequestById($vacationRequestId);

        if ($this->service->hasErrors()) {
            $this->errors[] = 'Не удается удалить заявку на отпуск';
            return false;
        }

        return $result;
    }
}
