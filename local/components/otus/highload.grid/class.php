<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Data\Cache as Cache;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity\Query;

class HighloadGrid extends \CBitrixComponent implements Controllerable
{
    public function configureActions()
    {
        return [];
    }

    public function onPrepareComponentParams($arParams)
    {
        $result = [
            'CACHE_TIME' => isset($arParams['CACHE_TIME']) ? $arParams['CACHE_TIME'] : 36000000,
            'CACHE_TYPE' => isset($arParams['CACHE_TYPE']) ? $arParams['CACHE_TYPE'] : 'A',
        ];
        return $result;
    }

    private function getElementActions($fields)
    {
        $actions = [];

        $actions[] = [
            'text' => 'Удалить',
            'onclick' => "BX.Otus.HighloadGrid.deleteItem({$fields['COLOR_ID']})",
        ];

        return $actions;
    }

    private function getHeaders()
    {
        return [
            [
                'id' => 'COLOR_ID',
                'name' => 'ID цвета',
                'sort' => 'COLOR_ID',
                'default' => true,
            ],
            [
                'id' => 'COLOR_NAME',
                'name' => 'Название цвета',
                'sort' => 'COLOR_NAME',
                'default' => true,
            ],
            [
                'id' => 'COLOR_HEX',
                'name' => 'HEX код цвета',
                'sort' => 'COLOR_HEX',
                'default' => true,
            ],
        ];
    }

    public function executeComponent()
    {
        $cache = Cache::createInstance();

        if ($cache->initCache($this->arParams['CACHE_TIME'], 'highloadGrid' . CurrentUser::get()->getId())) {
            $this->arResult = $cache->getVars();
        } else {
            $this->arResult['HEADERS'] = $this->getHeaders();

            $this->arResult['FILTER_ID'] = 'SAMPLE_GRID';

            $gridOptions = new \Bitrix\Main\Grid\Options($this->arResult['FILTER_ID']);
            $navParams = $gridOptions->getNavParams();

            $nav = new PageNavigation($this->arResult['FILTER_ID']);
            $nav->allowAllRecords(true)
                ->setPageSize($navParams['nPageSize'])
                ->initFromUri();

            $filterOption = new Bitrix\Main\UI\Filter\Options($this->arResult['FILTER_ID']);
            $filterData = $filterOption->getFilter([]);

            $filter = [];

            foreach ($filterData as $k => $v) {
                if ($k == 'COLOR_NAME') {
                    $filter['%UF_COLOR_NAME'] = $v;
                }
            }

            Loader::includeModule('highloadblock');

            $dbHL = HL\HighloadBlockTable::getList([
                'filter' => [
                    'NAME' => 'Colors'
                ],
            ]);

            if ($arItem = $dbHL->Fetch()) {
                $hlId = $arItem['ID'];
            }

            $hlbl = $hlId;
            $hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();

            $entity = HL\HighloadBlockTable::compileEntity($hlblock);
            $entityClassName = $entity->getDataClass();

            $q = new Query($entityClassName);
            $q->setSelect(array('*'));
            $q->registerRuntimeField(
                'RAND', [
                    'data_type' => 'float',
                    'expression' =>
                    ['RAND()']
                ]
            );
            $q->addOrder('RAND', 'ASC');
            $colors = $q->exec();

            $preparedElements = [];

            while ($color = $colors->fetch()) {
                $preparedElements[$color['ID']]['COLOR_ID'] = $color['ID'];
                $preparedElements[$color['ID']]['COLOR_NAME'] = $color['UF_COLOR_NAME'];
                $preparedElements[$color['ID']]['COLOR_HEX'] = $color['UF_COLOR_HEX'];
            }

            foreach ($preparedElements as $book) {
                $actions = $this->getElementActions($book);
                $this->arResult['GRID_LIST'][] = [
                    'data' => [
                        'COLOR_ID' => $book['COLOR_ID'],
                        'COLOR_NAME' => $book['COLOR_NAME'],
                        'COLOR_HEX' => $book['COLOR_HEX'],
                    ],
                    'actions' => $actions,
                ];
            }

            $this->arResult['UI_FILTER'] = [
                [
                    'id' => 'COLOR_NAME',
                    'name' => 'Название цвета',
                    'type' => 'string',
                    'default' => true,
                    'value' => '',
                ],
            ];

            $this->arResult['NAV'] = $nav;

            $cache->endDataCache($this->arResult);

            $this->includeComponentTemplate();

        }
    }

    public function deleteAction($id): bool
    {
        Loader::includeModule('highloadblock');

        $hlbl = 2;
        $hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();

        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entityClassName = $entity->getDataClass();

        return $entityClassName::Delete($id)->isSuccess();
    }
}
