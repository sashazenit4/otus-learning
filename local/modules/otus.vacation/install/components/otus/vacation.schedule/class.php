<?php

namespace Otus\Components;

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\ErrorableImplementation;
use Bitrix\Main\Loader;
use Otus\Vacation\VacationReport;

Loader::includeModule('otus.vacation');
class VacationScheduleComponent extends \CBitrixComponent implements Controllerable
{
    public function configureActions()
    {
        return [];
    }

    public function generateReportAction(?string $year)
    {
        $result = new \Bitrix\Main\Result();
        $report = new VacationReport();

        $year = intval($year);

        $report->setYear($year);

        if ($report->generateDocument()) {
            $name = $this->generateFilePath();
            $file = $report->makeResultFile($name);
            $resultContent = file_get_contents($file);
            $result->setData([
                'file_name' => 'report',
                'file_format' => 'xlsx',
                'result' => $report->getDataApplication() . base64_encode($resultContent),
            ]);
        }

        return $result->getData();

    }

    private function generateFilePath(?string $dir = null, ?string $name = null): string
    {
        if (empty($dir)) {
            $dir = sys_get_temp_dir();
        }

        if (empty($name)) {
            $name = uniqid();
        }

        return "{$dir}/{$name}";
    }

}