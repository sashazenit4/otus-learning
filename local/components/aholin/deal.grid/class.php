<?php

use Bitrix\Main\Grid;
use Bitrix\Main\Grid\Panel\Types;
use Bitrix\Main\UI;

class DealGridComponent extends \CBitrixComponent
{
    const GRID_ID = 'DEAL_GRID';

    public function executeComponent()
    {
        $grid_id = self::GRID_ID;
        $grid_options = new Grid\Options($grid_id);

        $grid_filter = $this->getFilterFields();

        $entityRepository = $this->getEntityRepository();

        $filter = $this->getEntityFilter($grid_id, $grid_filter);

        $select = $this->getEntitySelect();

        $sort = $this->getSorting($grid_options);

        $nav = $this->initNav($grid_options);

        $action_panel = $this->getActionPanel();

        $elements = $entityRepository::getList([
            'filter' => $filter,
            'select' => $select,
            'order' => $sort,
            'count_total' => true,
            'offset' => $nav->getOffset(),
            'limit' => $nav->getLimit(),
        ]);

        $nav->setRecordCount($elements->getCount());

        $grid_rows = [];

        foreach ($elements as $element) {
            $prepared_element = $this->getPreparedElement($element);

            $actions = $this->getElementActions($element);

            $row = [
                'id' => $element['ID'],
                'data' => $element,
                'columns' => $prepared_element,
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
        $this->arResult['ACTION_PANEL'] = $action_panel;

        $this->includeComponentTemplate();
    }

    public function getEntityRepository(): \Bitrix\Crm\DealTable
    {
        return new \Bitrix\Crm\DealTable();
    }

    public function initNav($grid_options): UI\PageNavigation
    {
        $navParams = $grid_options->GetNavParams();

        $grid_id = $grid_options->getid();

        $nav = new UI\PageNavigation($grid_id);

        $pageSizes = [];
        foreach (['5', '10', '20', '30', '50', '100'] as $index) {
            $pageSizes[] = ['NAME' => $index, 'VALUE' => $index];
        }

        $nav->allowAllRecords(true)
            ->setPageSize($navParams['nPageSize'])
            ->setPageSizes($pageSizes)
            ->initFromUri();

        return $nav;
    }

    public function getSorting($grid): array
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

    public function getEntityFilter($grid_id, $grid_filter): array
    {
        return $this->prepareFilter($grid_id, $grid_filter);
    }

    public function getEntitySelect(): array
    {
        return ['*'];
    }

    public function getPreparedElement($fields): array
    {
        return $fields;
    }

    public function getElementActions($fields): array
    {
        return [];
    }

    private function getFilterFields(): array
    {
        $filterFields = [
            [
                'id' => 'ID',
                'name' => 'id',
                'type' => 'number',
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
                'name' => 'id сделки',
                'sort' => 'ID',
                'default' => true
            ],
        ];

        return $columns;
    }

    protected function getActionPanel(): array
    {
        return [];
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
                    '%ID' => $filter['FIND']
                ]
            ];

            if (!empty($filterPrepared)) {
                $filterPrepared[] = $findFilter;
            } else {
                $filterPrepared = $findFilter;
            }
        }

        $filterPrepared['STAGE_ID'] = $this->arParams['STAGE_ID'];

        return $filterPrepared;
    }
}
