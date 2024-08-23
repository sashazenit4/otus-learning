<?php
include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

\Bitrix\Main\Loader::includeModule('crm');
$dealFields = [
    'TITLE' => 'Тестовая сделка',
    'STAGE_ID' => 'C2:PREPARATION',
];
$idDeal = 15;
$updateResult = \Bitrix\Crm\DealTable::update($idDeal, $dealFields);

if ($updateResult->isSuccess()) {
    echo 'Сделка успешно обновлена';
} else {
    $errorMessage = 'Ошибка обновления: ';
    foreach ($updateResult->getErrorMessages() as $error) {
        $errorMessage .= $error . '<br>';
    }
    echo $errorMessage;
}
