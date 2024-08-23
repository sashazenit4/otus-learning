<?php

use Bitrix\Main\Loader;
use Bitrix\Main\UserTable;

Loader::includeModule('iblock');
class DepartmentHeadListComponent extends \CBitrixComponent
{
    public function executeComponent()
    {
        $this->arResult = $this->getResult();
        $this->includeComponentTemplate();
    }

    private function getResult(): array
    {
        return [
            'EMPLOYEES' => $this->getDepartmentHeads(),
        ];
    }

    private function getDepartmentHeads(): array
    {
        $structureIblockId = \Bitrix\Main\Config\Option::get('intranet', 'iblock_structure', 3);
        $orgStructureEntity = \Bitrix\Iblock\Model\Section::compileEntityByIblock($structureIblockId);

        $rsDepartments = $orgStructureEntity::getList([
            'select' => [
                'ID',
                'NAME',
                'UF_HEAD',
                'USER_INFO_' => 'user',
            ],
            'runtime' => [
                new \Bitrix\Main\ORM\Fields\Relations\Reference('user', UserTable::class,
                [
                    '=this.UF_HEAD' => 'ref.ID'
                ],
                [
                    'join_type' => 'INNER'
                ]),
            ],
        ])->fetchAll();

        $users = [];
        foreach ($rsDepartments as $dep) {
            $users[] = [
                'ID' => $dep['USER_INFO_ID'],
                'NAME' => $dep['USER_INFO_NAME'],
                'LAST_NAME' => $dep['USER_INFO_LAST_NAME'],
                'PERSONAL_PHOTO' => !empty($dep['USER_INFO_PERSONAL_PHOTO']) ? \CFile::GetPath($dep['USER_INFO_PERSONAL_PHOTO']) : 0,
            ];
        }
        return $users;
    }
}
