<?php

namespace Otus\Components;

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Grid;
use Bitrix\Main\Loader;
use Bitrix\Main\UI;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\ErrorableImplementation;
use enoffspb\BitrixEntityManager\EntityManagerInterface;
use Otus\Vacation\AccessManager;
use Otus\Vacation\Controller;
use Otus\Vacation\Entity\VacationRequest;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('otus.vacation')) {
    \showError(Loc::getMessage('MODULE_NOT_INSTALLED_ERROR'));
    die();
}

class VacationGridComponent extends \CBitrixComponent implements Controllerable
{
    use ErrorableImplementation;

    const GRID_ID = 'VACATION_GRID';

    const PAGE_SIZE = 15;

    private int $userId;

    protected Controller $controller;

    protected EntityManagerInterface $entityManager;

    protected AccessManager $accessManager;

    public function configureActions()
    {
        return [];
    }

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
        if (!$this->accessManager->can($this->userId, 'viewVacationList')) {
            \showError(Loc::getMessage('NO_ACCESS_ERROR'));
            return;
        }

        $this->arResult['permissions']['CAN_CREATE_VACATION_REQUEST'] = $this->accessManager->can($this->userId, 'createVacationRequest');

        $grid_id = self::GRID_ID;
        $grid_options = new Grid\Options($grid_id);

        $grid_filter = $this->getFilterFields();

        $filter = $this->getEntityFilter($grid_id, $grid_filter);

        $sort = $this->getSorting($grid_options);

        $page_size = $this->arParams['PAGE_SIZE'] ?? self::PAGE_SIZE;

        $nav = $this->initNav($grid_options, $page_size);

        $params = [
            "filter" => $filter,
            "order" => $sort
        ];

        $limits = [
            "offset" => $nav->getOffset(),
            "limit" => $nav->getLimit()
        ];

        $totalParams = array_merge($params, $limits);
        $vacationRequestCollection = $this->controller->getVacationRequestCollection($totalParams);
        $nav->setRecordCount(count($this->controller->getVacationRequestCollection($params)));

        $grid_rows = [];

        /** @var VacationRequest $vacationRequest */
        foreach ($vacationRequestCollection as $vacationRequest) {
            $prepared_element = $this->getPreparedElement($vacationRequest);

            $actions = $this->getElementActions($vacationRequest);

            $row = [
                'id' => $vacationRequest->id,
                'data' => $prepared_element,
                'editable' => 'Y',
                'actions' => $actions
            ];

            $grid_rows[] = $row;
        }

        $this->arResult['NAV'] = $nav;

        $this->arResult['GRID_ID'] = $grid_id;
        $this->arResult['GRID_FILTER'] = $grid_filter;
        $this->arResult['GRID_COLUMNS'] = $this->getGridColumns();
        $this->arResult['ROWS'] = $grid_rows;

        $this->includeComponentTemplate();
    }

    public function initNav($grid_options, $page_size)
    {
        $grid_id = $grid_options->getid();

        $nav = new UI\PageNavigation($grid_id);

        $nav->allowAllRecords(true)
            ->setPageSize($page_size)
            ->initFromUri();

        return $nav;
    }

    public function getSorting($grid)
    {
        $sort = $grid->GetSorting([
            'sort' => [
                'ID' => 'DESC'
            ],
            'vars' => [
                'by' => 'by',
                'order' => 'order'
            ]
        ]);

        return $sort['sort'];
    }

    public function getEntityFilter($grid_id, $grid_filter)
    {
        return $this->prepareFilter($grid_id, $grid_filter);
    }

    public function getPreparedElement(VacationRequest $item)
    {
        $userInfo = \CUser::getById($item->requestedUser)->fetch();
        $userLink = '<a href="/company/personal/user/' . $userInfo['ID'] . '/">' . $userInfo['NAME'] . ' ' . $userInfo['LAST_NAME'] . '</a>';

        $status = '';

        switch ($item->status) {
            case 'REVISION':
                $status = 'Выбор сотрудником';
                break;
            case 'AGREED':
                $status = 'Заявка утверждена';
                break;
            case 'APPROVAL':
                $status = 'Заявка на согласовании';
                break;
            case 'REJECTED':
                $status = 'Заявка отклонена';
                break;
            case 'CLOSED':
                $status = 'Заявка закрыта';
                break;
            default:
                $status = 'Неизвестный статус';
                break;
        }

        $fields = [
            'ID' => $item->id,
            'CREATED_AT' => $item->createdAt,
            'STATUS' => $status,
            'REQUESTED_USER' => $userLink,
            'DESCRIPTION' => $item->description,
        ];

        return $fields;
    }

    public function getElementActions(VacationRequest $item)
    {
        $actions = [];

        if ($this->accessManager->can($this->userId, 'viewVacationRequest', $item)) {
            $actions[] = [
                'text' => Loc::getMessage('VIEW_ELEMENT_HINT'),
                'onclick' => "BX.Otus.Vacation.List.showCreateRequestForm({$item->id})",
                'default' => true
            ];
        }

        if ($this->accessManager->can($this->userId, 'deleteVacationRequest', $item)) {
            $actions[] = [
                'text' => 'Удалить отпуск',
                'onclick' => "BX.Otus.Vacation.List.deleteRequestById({$item->id})",
                'default' => true
            ];
        }

        return $actions;
    }

    private function getFilterFields(): array
    {
        $filterFields = [
            [
                'id' => 'CREATED_AT',
                'name' => Loc::getMessage('DATE_CREATE_FILTER_FIELD_TITLE'),
                'type' => 'date',
                'default' => true
            ],
            [
                'id' => 'REQUESTED_USER',
                'name' => Loc::getMessage('REQUESTED_USER_FILTER_FIELD_TITLE'),
                'type' => 'entity_selector',
                'params' => [
                    'multiple'      => 'Y',
					'dialogOptions' => [
						'height'       => 200,
						'entities'     => [
							[
								'id'      => 'user',
								'options' => [
									'inviteEmployeeLink' => false,
									'intranetUsersOnly'  => true,
								]
							],
						],
						'showAvatars'  => true,
						'dropdownMode' => false,
					],
                ],
                'default' => true
            ],
        ];

        return $filterFields;
    }

    private function getGridColumns(): array
    {
        $columns = [
            [
                'id' => 'ID',
                'name' => 'ID',
                'sort' => 'ID',
                'default' => true
            ],
            [
                'id' => 'CREATED_AT',
                'name' => Loc::getMessage('DATE_CREATE_GRID_FIELD_TITLE'),
                'sort' => 'CREATED_AT',
                'default' => true
            ],
            [
                'id' => 'STATUS',
                'name' => Loc::getMessage('STATUS_GRID_FIELD_TITLE'),
                'sort' => 'STATUS',
                'default' => true
            ],
            [
                'id' => 'REQUESTED_USER',
                'name' => Loc::getMessage('REQUESTED_USER_GRID_FIELD_TITLE'),
                'sort' => 'REQUESTED_USER',
                'default' => true
            ],
            [
                'id' => 'DESCRIPTION',
                'name' => Loc::getMessage('REQUESTED_USER_GRID_FIELD_DESCRIPTION'),
                'sort' => 'DESCRIPTION',
                'default' => true
            ],
        ];

        return $columns;
    }

    private function prepareFilter($grid_id, $grid_filter): array
    {
        $filter = [];

        $filterOption = new \Bitrix\Main\UI\Filter\Options($grid_id);
        $filterData = $filterOption->getFilter([]);

        foreach ($filterData as $k => $v) {
            $filter[$k] = $v;
        }

        $filterPrepared = \Bitrix\Main\UI\Filter\Type::getLogicFilter($filter, $grid_filter);

        if (!empty($filter['FIND'])) {
            $findFilter = [
                'LOGIC' => 'OR',
                [
                    '%DESCRIPTION' => $filter['FIND']
                ]
            ];

            if (!empty($filterPrepared)) {
                $filterPrepared[] = $findFilter;
            } else {
                $filterPrepared = $findFilter;
            }
        }

        return $filterPrepared;
    }
}
