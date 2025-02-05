<?php
namespace Otus\Agents;

use Bitrix\Main\Loader;
use \Bitrix\Main\Type\DateTime;

class Tasks
{
    public static function updateOverdueTasks(): string
    {
        Loader::includeModule('tasks');
        $rawTasks = \CTasks::GetList([
            'DEADLINE' => 'ASC'
        ],
        [
            '<DEADLINE' => new DateTime(),
        ],
        [
            'ID', 'TASKS'
        ]);

        while ($task = $rawTasks->fetch()) {
            $taskId = $task['ID'];
            $currentDeadline = $task['DEADLINE'];
            $newDeadline = new DateTime();

            if ($currentDeadline) {
                $newDeadline = $newDeadline->add('1 day');
                $taskObject = new \CTasks();
                $taskObject->Update($taskId, [
                    'DEADLINE' => $newDeadline,
                ]);
                if ($taskObject->LAST_ERROR) {
                    \Bitrix\Main\Diag\Debug::dumpToFile('Ошибка при обновлении задаи с пропущенным дедлайном: ' . $taskObject->LAST_ERROR);
                }
            }
        }

        return '\Otus\Agents\Tasks::updateOverdueTasks()';
    }
}
