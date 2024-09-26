<?php

namespace Otus\Vacation;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Html;

abstract class BaseXlsxReport extends BaseReport
{
    const DOCUMENT_FORMAT = 'xlsx';

    const DATA_APPLICATION = 'data:application/vnd.ms-excel;base64,';

    const DEAL_CATEGORIES = [
        0 => 'income',
        1 => 'expence'
    ];

    protected $spreadsheet;

    public function excelColumnRange($lower, $upper)
    {
        ++$upper;
        for ($i = $lower; $i !== $upper; ++$i) {
            yield $i;
        }
    }

    public function __construct()
    {
        \PhpOffice\PhpSpreadsheet\Shared\StringHelper::setDecimalSeparator(',');
        \PhpOffice\PhpSpreadsheet\Shared\StringHelper::setThousandsSeparator(' ');
        \PhpOffice\PhpSpreadsheet\Settings::setLocale('ru');

        $this->spreadsheet = new Spreadsheet();
        $this->writer = new Xlsx($this->spreadsheet);
    }

    /**
     * Формирование Xlsx документа
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function makeResultFile(string $outputFile): string
    {
        $this->saveResultFile($this->spreadsheet, $outputFile);

        return $outputFile;
    }

    /**
     * Формирование Html документа
     * @return string
     */
    public function makeHtmlFile(string $outputFile): string
    {
        $this->saveHtmlFile($this->spreadsheet, $outputFile);

        return $outputFile;
    }

    /**
     * Сохранение Xlsx документа
     * @param Spreadsheet $spreadsheet
     * @param string $outputFile
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    protected function saveResultFile(Spreadsheet $spreadsheet, string $outputFile)
    {
        $this->writer = new Xlsx($spreadsheet);
        $this->writer->save($outputFile);
    }

    /**
     * Сохранение Html документа
     * @param Spreadsheet $spreadsheet
     * @param string $outputFile
     */
    protected function saveHtmlFile(Spreadsheet $spreadsheet, string $outputFile)
    {
        $this->writer = new Html($spreadsheet);
        $this->writer->save($outputFile);
    }


    public function getCustomStyleHtml(): ?string
    {
        $style = "<style> 
             html {background: transparent !important;} 
             #report-wrapper table{width: 100%;}
             #report-wrapper table td{
                font-size: 12px !important;
             }
        </style>";

        return $style;
    }

    /**
     * Шаблон для числовых (цен) полей
     * @return string
     */
    public function getNumberFormatTemplate(): string
    {
        return \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1;
    }

    /**
     * Установка числового формата
     * @param Style $style
     */
    public function setNumberStyle(Style $style)
    {
        $style->getNumberFormat()->setFormatCode($this->getNumberFormatTemplate());
    }


    /**
     * Добавление заголовка
     * @param array $params
     * @return void
     */
    public function insertTitleRow(array $params = [])
    {
        try {
            $sheet = $this->spreadsheet->getActiveSheet();

            $lastColumn = $sheet->getHighestColumn();

            $title = $this->prepareTitle($params);

            $sheet->insertNewRowBefore(1, 3);

            $sheet->mergeCells("A1:{$lastColumn}3");
            $sheet->setCellValue("A1", $title);
            $sheet->getStyle('A1')->getFont()->setBold(true);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal('center')->setVertical('center');
        } catch (\Throwable $e) {
            // @TODO обработать ошибку
        }
    }
}