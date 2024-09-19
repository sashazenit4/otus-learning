<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

/**
 * @var CMain $APPLICATION
 * @var array $arResult
 */

use PhpOffice\PhpSpreadsheet\Spreadsheet; // Импорт класса Spreadsheet из библиотеки PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Writer\Xlsx; // Импорт класса Xlsx из библиотеки PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$spreadSheet = new Spreadsheet(); // Создание нового объекта класса Spreadsheet
$writer = new Xlsx($spreadSheet); // Создание нового объекта класса Xlsx с передачей объекта $spreadSheet в конструктор
$activeSheet = $spreadSheet->getActiveSheet(); // Получение активного листа из объекта $spreadSheet

#$spreadSheet->setActiveSheetIndex(2);

$column = 'A'; // Инициализация переменной $column со значением 'A'
foreach ($arResult['HEADERS'] as $value) { // Цикл по элементам массива $arResult['COLUMNS']
    $activeSheet->setCellValue($column.'1', $value['name']); // Установка значения ячейки с помощью метода setCellValue для текущего столбца и строки 1
    $column++; // Инкрементация переменной $column для перехода к следующему столбцу
}

$headersStyleArray = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => '444444'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true,
    ],
];

$activeSheet->getStyle('A1:' . $column . '1')->applyFromArray($headersStyleArray);

$row = 2; // Установка начального значения переменной $row равным 2 для начала заполнения строк со второй
foreach ($arResult['GRID_LIST'] as $value) { // Цикл по элементам массива $arResult['LIST']
    $column = 'A'; // Сброс переменной $column в начало столбца перед каждой строкой
    foreach ($value['data'] as $itemText) { // Цикл по элементам массива $value['data']
        $activeSheet->setCellValue($column.$row, $itemText); // Установка значения ячейки с помощью метода setCellValue для текущего столбца и строки $row
        $column++; // Инкрементация переменной $column для перехода к следующему столбцу
    }
    $row++; // Инкрементация переменной $row для перехода к следующей строке
}

foreach ($activeSheet->getColumnIterator() as $column) {
    $activeSheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
}

$writer->save('test.xlsx'); // Сохранение файла в формате Xlsx в поток вывода
