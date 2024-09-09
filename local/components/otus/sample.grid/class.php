<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\Engine\CurrentUser;

class SampleGrid extends \CBitrixComponent implements Controllerable
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
            'onclick' => "BX.Otus.SampleGrid.deleteItem({$fields['ID']})",
        ];

        return $actions;
    }

    private function getHeaders()
    {
        return [
            [
                'id' => 'BOOK_ID',
                'name' => 'ID книги',
                'sort' => 'BOOK_ID',
                'default' => true,
            ],
            [
                'id' => 'BOOK_NAME',
                'name' => 'Название книги',
                'sort' => 'BOOK_NAME',
                'default' => true,
            ],
            [
                'id' => 'PUBLISHING_NAME',
                'name' => 'Название издательства',
                'sort' => 'PUBLISHING_NAME',
                'default' => true,
            ],
            [
                'id' => 'AUTHOR_NAME',
                'name' => 'Имя автора',
                'sort' => 'AUTHOR_NAME',
                'default' => true,
            ],
            [
                'id' => 'COPIES_CNT',
                'name' => 'Тираж',
                'sort' => 'COPIES_CNT',
                'default' => true,
            ],
            [
                'id' => 'SALE_DATE_END',
                'name' => 'Дата окончания продаж',
                'sort' => 'SALE_DATE_END',
                'default' => true,
            ],
        ];
    }

    public function executeComponent()
    {
        $cache = \Bitrix\Main\Data\Cache::createInstance();

        if ($cache->initCache($this->arParams['CACHE_TIME'], 'sampleGrid' . CurrentUser::get()->getId())) {
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
                if ($k == 'BOOK_NAME') {
                    $filter['%TITLE'] = $v;
                }
                if ($k == 'PUBLISHING_NAME') {
                    $filter['%PUBLISHING.TITLE'] = $v;
                }
                if ($k == 'SALE_DATE_END_to') {
                    $filter['<=SALE_DATE_END'] = $v;
                }
                if ($k == 'SALE_DATE_END_from') {
                    $filter['>=SALE_DATE_END'] = $v;
                }
            }

            $books = \Otus\Orm\BookTable::getList([
                'filter' => $filter,
                'select' => [
                    'ID',
                    'TITLE',
                    'P_TITLE' => 'PUBLISHING.TITLE',
                    'COPIES_CNT',
                    'SALE_DATE_END',
                    'AUTHOR_NAME' => 'Otus\Orm\BookAuthorTable:BOOK.AUTHOR.FIRST_NAME',
                    'AUTHOR_LAST_NAME' => 'Otus\Orm\BookAuthorTable:BOOK.AUTHOR.LAST_NAME',
                    'AUTHOR_SECOND_NAME' => 'Otus\Orm\BookAuthorTable:BOOK.AUTHOR.SECOND_NAME',
                ],
                'limit' => $nav->getLimit(),
                'offset' => $nav->getOffset(),
                'count_total' => true,
                'cache' => [
                    'ttl' => 3600,
                ],
            ]);

            $nav->setRecordCount($books->getCount());

            $preparedElements = [];

            while ($book = $books->fetch()) {
                if (empty($preparedElements[$book['ID']]['AUTHORS']) and $preparedElements[$book['ID']]['AUTHORS'] !== '') {
                    $preparedElements[$book['ID']]['AUTHORS'] = '';
                    $preparedElements[$book['ID']]['AUTHORS'] .= $book['AUTHOR_LAST_NAME'] . ' ' . $book['AUTHOR_NAME'] . ' ' . $book['AUTHOR_SECOND_NAME'] . ', ';
                } else {
                    $preparedElements[$book['ID']]['AUTHORS'] .= $book['AUTHOR_LAST_NAME'] . ' ' . $book['AUTHOR_NAME'] . ' ' . $book['AUTHOR_SECOND_NAME'] . ', ';
                }
                $preparedElements[$book['ID']]['BOOK_ID'] = $book['ID'];
                $preparedElements[$book['ID']]['BOOK_NAME'] = $book['TITLE'];
                $preparedElements[$book['ID']]['PUBLISHING_NAME'] = $book['P_TITLE'];
                $preparedElements[$book['ID']]['COPIES_CNT'] = $book['COPIES_CNT'];
                $preparedElements[$book['ID']]['SALE_DATE_END'] = $book['SALE_DATE_END'];
            }

            foreach ($preparedElements as $book) {
                $actions = $this->getElementActions($book);
                $this->arResult['GRID_LIST'][] = [
                    'data' => [
                        'BOOK_ID' => $book['BOOK_ID'],
                        'BOOK_NAME' => $book['BOOK_NAME'],
                        'PUBLISHING_NAME' => $book['PUBLISHING_NAME'],
                        'AUTHOR_NAME' => substr($book['AUTHORS'], 0, -2),
                        'COPIES_CNT' => $book['COPIES_CNT'],
                        'SALE_DATE_END' => $book['SALE_DATE_END']->format('d.m.Y'),
                    ],
                    'actions' => $actions,
                ];
            }


            $this->arResult['UI_FILTER'] = [
                [
                    'id' => 'BOOK_NAME',
                    'name' => 'Название книги',
                    'type' => 'string',
                    'default' => true,
                    'value' => '',
                ],
                [
                    'id' => 'PUBLISHING_NAME',
                    'name' => 'Название издательства',
                    'type' => 'string',
                    'default' => true,
                    'value' => '',
                ],
                [
                    'id' => 'SALE_DATE_END',
                    'name' => 'Дата окончания продаж',
                    'type' => 'date',
                    'default' => true,
                    'value' => 'now',
                ],
            ];

            $this->arResult['NAV'] = $nav;

            $cache->endDataCache($this->arResult);

            $this->includeComponentTemplate();

        }
    }

    public function deleteAction($id): bool
    {
        $result = \Otus\Orm\BookTable::delete($id);
        return !empty($result);
    }
}
