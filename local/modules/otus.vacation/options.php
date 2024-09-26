<?php
/**
 * to do lang files
 */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
Loc::loadMessages(__FILE__);

/**
 * Modules options
 */

// получаем идентификатор модуля
$request = HttpApplication::getInstance()->getContext()->getRequest();
$module_id = htmlspecialchars($request['mid'] != '' ? $request['mid'] : $request['id']);
// подключаем наш модуль
Loader::includeModule($module_id);

/*
 * Параметры модуля со значениями по умолчанию
 */
global $APPLICATION;

$arTabs = array(
    array(
        "DIV" => "main", "TAB" => "Модуль отпусков", "TITLE" => "Модуль отпусков - выбор групп",
    ),
    array(
        "DIV" => "types", "TAB" => "Типы отпусков", "TITLE" => "Модуль отпусков - выбор типов отпусков",
    ),
    array(
        "DIV" => "strict", "TAB" => "Настройки строгости", "TITLE" => "Модуль отпусков - строгий режим",
    ),
);

$tabControl = new \CAdminTabControl("tabControl", $arTabs);
?>

<?= bitrix_sessid_post(); ?>
<?php
$tabControl->Begin();
$tabControl->BeginNextTab();

$groups = \Bitrix\Main\GroupTable::getList([
    'select'=>[
        'ID',
        'NAME'
    ]
]);
$groupAr = [];
while($resGroup = $groups->Fetch()){
    $groupAr[] = $resGroup;
}

$users = \Bitrix\Main\UserTable::getList([
    'select'=>[
        'ID',
        'NAME',
        'LAST_NAME',
    ]
]);
$userAr = [];
while($resGroup = $users->Fetch()){
    $userAr[] = $resGroup;
}

?>
<form action="<?= $APPLICATION->getCurPage(); ?>?mid=<?=$module_id; ?>&lang=<?= LANGUAGE_ID; ?>" method="post">
    <?= bitrix_sessid_post(); ?>
    <div>
        <h4>Сотрудники</h4>
        <div class="select-item">
            <select name="employee_vacation">
                <?php foreach($groupAr as $groupItem): ?>
                    <?php if($groupItem['ID'] == Option::get($module_id, "otus_vacation_employee_id")):?>
                        <option value="<?=$groupItem['ID']?>" selected><?=$groupItem['NAME']?></option>
                    <?php else:?>
                        <option value="<?=$groupItem['ID']?>"><?=$groupItem['NAME']?></option>
                    <?php endif;?>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <h4>Модераторы отпусков</h4>
    <div class="select-item">
        <select name="moderator_vacation">
            <?php foreach($groupAr as $groupItem): ?>
                <?php if($groupItem['ID'] == Option::get($module_id, "otus_vacation_moderator_id")):?>
                    <option value="<?=$groupItem['ID']?>" selected><?=$groupItem['NAME']?></option>
                <?php else:?>
                    <option value="<?=$groupItem['ID']?>"><?=$groupItem['NAME']?></option>
                <?php endif;?>
            <?php endforeach; ?>
        </select>
    </div>
    <h4>Бухгалтер</h4>
    <div class="select-item">
        <select name="account_vacation">
            <?php foreach($userAr as $userItem): ?>
                <?php if($userItem['ID'] == Option::get($module_id, "otus_vacation_account_id")):?>
                    <option value="<?=$userItem['ID']?>" selected><?=$userItem['NAME']?></option>
                <?php else:?>
                    <option value="<?=$userItem['ID']?>"><?=$userItem['NAME'] . ' ' . $userItem['LAST_NAME']?></option>
                <?php endif;?>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="select-item" style="margin:15px 0">
        <input type="submit" name="apply" value="Сохранить" class="adm-btn-save" />
    </div>
</form>

<?php
$tabControl->EndTab();

$tabControl->BeginNextTab();

$absenceIblockId = Option::get('intranet', 'iblock_absence');

$filter = [
    'IBLOCK_ID' => $absenceIblockId,
    'NAME' => 'Тип отсутствия'
];

$rsProperties = \CIBlockProperty::getList([], $filter);

$absenceTypePropertyId = $rsProperties->fetch()['ID'];

$rsPropertyValues = \CIBlockProperty::getPropertyEnum($absenceTypePropertyId, ['ID' => 'ASC'], []);

$vacationTypes = [];

while ($type = $rsPropertyValues->fetch()) {
    $vacationTypes[$type['ID']] = $type['VALUE'];
    $lastSort = $type['SORT'];
}

$selectedTypes = explode(' ', Option::get($module_id, 'otus_vacation_selected_types', 1));

?>
<form action="<?= $APPLICATION->getCurPage(); ?>?mid=<?=$module_id; ?>&lang=<?= LANGUAGE_ID; ?>" method="post">
    <?= bitrix_sessid_post(); ?>
    <div>
        <h4>Типы отпусков</h4>

            <?php foreach($vacationTypes as $vacationTypeId => $vacationTypeValue): ?>
                <div class="select-item">
                    <?php if(in_array($vacationTypeId, $selectedTypes)):?>
                            <input name="vacation_types[]" type="checkbox" id="vacation_id_<?=$vacationTypeId?>" checked value="<?=$vacationTypeId?>">
                            <label for="vacation_id_<?=$vacationTypeId?>"><?=$vacationTypeValue?></label>
                        <?php else:?>
                            <input name="vacation_types[]" type="checkbox" id="vacation_id_<?=$vacationTypeId?>" value="<?=$vacationTypeId?>">
                            <label for="vacation_id_<?=$vacationTypeId?>"><?=$vacationTypeValue?></label>
                    <?php endif;?>
                </div>
            <?php endforeach; ?>
    </div>
    <div>
        <h4>Добавить новый тип отпуска</h4>
        <div class="select-item">
            <input name="vacation_type_add" type="text" value="" placeholder="отпуск прошлого года">
        </div>
    </div>
    <div class="select-item" style="margin:15px 0">
        <input type="submit" name="apply" value="Сохранить" class="adm-btn-save" />
    </div>
</form>
<div class="adm-info-message-wrap">
    <div class="adm-info-message">
        В Данном разделе вы можете выбрать те типы отпусков, которые будете использовать в работе модуля.
        Также здесь вы можете создать свой тип отпуска и добавить его к существующим в Битриксе из коробки.
        Для полного редаиктирования типов отпусков рекомендуем ознакомиться с
        <a target="_blank" href="https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=2895">материалами курса </a>.
    </div>
</div>
<?php
$tabControl->EndTab();
?>
<?php
$tabControl->BeginNextTab();

$strictChecking = Option::get($module_id, 'otus_vacation_strict_checking', 0);
?>
<form action="<?= $APPLICATION->getCurPage(); ?>?mid=<?=$module_id; ?>&lang=<?= LANGUAGE_ID; ?>" method="post">
 <?= bitrix_sessid_post(); ?>
 <div>
     <h4>Проверять даты отпусков строго?</h4>
         <div class="select-item">
             <select name="otus_vacation_strict_checking" id="otus_vacation_strict_checking">
                 <option <?=$strictChecking == 1 ? 'selected' : ''?> value="1">Да</option>
                 <option <?=$strictChecking == 0 ? 'selected' : ''?> value="0">Нет</option>
             </select>
         </div>
 </div>
 <div class="select-item" style="margin:15px 0">
     <input type="submit" name="apply" value="Сохранить" class="adm-btn-save" />
 </div>
</form>
<?php
if ($request->isPost() && check_bitrix_sessid()) {
    $employeeId = $request->getPost('employee_vacation');
    $employeeId = (int)$employeeId;
    $moderatorId = $request->getPost('moderator_vacation');
    $moderatorId = (int)$moderatorId;
    $accountId = $request->getPost('account_vacation');
    $accountId = (int)$accountId;

    if (!empty($employeeId)) {
        Option::set($module_id, 'otus_vacation_employee_id', $employeeId);
    }

    if (!empty($moderatorId)) {
        Option::set($module_id, 'otus_vacation_moderator_id', $moderatorId);
    }

    if (!empty($accountId)) {
        Option::set($module_id, 'otus_vacation_account_id', $accountId);
    }

    $vacationTypes = $request->getPost('vacation_types');

    if (!empty($vacationTypes)) {
        $vacationTypesString = !empty($vacationTypes) ? implode(' ', $vacationTypes) : "1";
        Option::set($module_id, 'otus_vacation_selected_types', $vacationTypesString);
    }

    $newVacationType = $request->getPost('vacation_type_add');

    if (!empty($newVacationType)) {
        $model = new CIBlockPropertyEnum();
    
        $fields = [
            'PROPERTY_ID' => $absenceTypePropertyId,
            'VALUE' => $newVacationType,
            'XML_ID' => \CUtil::translit($newVacationType, 'ru', ['change_case' => 'U']),
            'SORT' => $lastSort + 100,
            'DEF' => 'N',
        ];
    
        $model->add($fields);
    }

    $strict = $request->getPost('otus_vacation_strict_checking');

    if (!($strict !== '0' && $strict !== '1')) {
        Option::set($module_id, 'otus_vacation_strict_checking', $strict);
    }

    LocalRedirect($APPLICATION->getCurPage().'?mid='.$module_id.'&lang='.LANGUAGE_ID);
}
?>
<?php
$tabControl->EndTab();
?>
<?php
$tabControl->End();
?>
