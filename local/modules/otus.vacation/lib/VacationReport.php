<?php
namespace Otus\Vacation;

use Bitrix\Main\Loader;
use Bitrix\Main\UserTable;

class VacationReport extends BaseXlsxReport
{
    protected array $users;

    protected int $year = 0;

    protected const MIN_YEAR = 2001;

    public function __construct()
    {
        $this->users = $this->getUserArray();

        parent::__construct();
    }

    protected function getData(?int $year): array
    {
        Loader::includeModule('iblock');

        $filter = [
            'IBLOCK_ID' => \Bitrix\Main\Config\Option::get('intranet', 'iblock_absence', 1),
            'PROPERTY_ABSENCE_TYPE' => explode(' ', \Bitrix\Main\Config\Option::get('otus.vacation', 'otus_vacation_selected_types', 1)),
        ];

        if (!empty($year)) {
            $filter['>=DATE_ACTIVE_FROM'] = "01.01.$year 00:00:00";
            $filter['<=DATE_ACTIVE_FROM'] = "31.12.$year 23:59:99";
        }
        
        $rsAbsenceData = \CIBlockElement::getList([], $filter, false, false, []);

        $absenceData = [];

        while ($absence = $rsAbsenceData->GetNextElement()) {
            $absenceData[$absence->getProperties()['USER']['VALUE']][] = [
                'DATE_ACTIVE_FROM' => $absence->getFields()['DATE_ACTIVE_FROM'],
                'DATE_ACTIVE_TO' => $absence->getFields()['DATE_ACTIVE_TO']
            ];
        }

        $returnAbsenceInfo = [];

        foreach ($absenceData as $userId => $absencesByUser) {
            foreach ($absencesByUser as $absenceRow) {
                $dateFromObject = new \DateTime($absenceRow['DATE_ACTIVE_FROM']);
                $dateToObject = new \DateTime($absenceRow['DATE_ACTIVE_TO']);
                $returnAbsenceInfo[] = [
                    'USER_ID' => $userId,
                    'DATE_FROM' => $absenceRow['DATE_ACTIVE_FROM'],
                    'DATE_TO' => $absenceRow['DATE_ACTIVE_TO'],
                    'DAYS_COUNT' => date_diff($dateFromObject, $dateToObject)->d + 1,
                ];
            }
        }

        return $returnAbsenceInfo;
    }

    public function generateDocument(): bool
    {
        $data = $this->getData($this->year);

        $sheet = $this->spreadsheet->getActiveSheet();

        $sheet->setTitle('Отчёт по отпускам');

        $sheet->setCellValue('A1', 'Сотрудник');
        $sheet->setCellValue('B1', 'Дата начала');
        $sheet->setCellValue('C1', 'Дата окончания');
        $sheet->setCellValue('D1', 'Продолжительность');

        foreach ($data as $key => $row) {
            $sheet->setCellValue('A' . strval($key + 2), $this->users[$row['USER_ID']]);
            $sheet->setCellValue('B' . strval($key + 2), $row['DATE_FROM']);
            $sheet->setCellValue('C' . strval($key + 2), $row['DATE_TO']);
            $sheet->setCellValue('D' . strval($key + 2), $row['DAYS_COUNT']);
        }

        foreach ($this->excelColumnRange("A", "D") as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        return true;
    }

    protected function getUserArray()
    {
        $rsUsers = UserTable::getList([]);

        $users = [];

        while ($user = $rsUsers->fetch()) {
            $users[$user['ID']] = $user['NAME'] . ' ' . $user['LAST_NAME'] . ' ' . $user['SECOND_NAME'];
        }

        return $users;
    }

    public function setYear(int $year): void
    {
        if ($year > $this::MIN_YEAR) {
            $this->year = $year;
        }
    }
}