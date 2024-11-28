<?php

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

Loader::includeModule('highloadblock');

$arElementFields = [
    'UF_COLOR_NAME' => 'Зеленый',
    'UF_COLOR_HEX' => '#00FF00',
];

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

$updateResult = $entityClassName::update(2, $arElementFields);

$ID = $updateResult->getID();
$bSuccess = $updateResult->isSuccess();
if ($bSuccess)
    echo "Highload element {$ID} was updated!";
else {
    $arErrors = $updateResult->getErrorMessages();
    foreach ($arErrors as $error) {
        echo "ERROR: " . $error . "<br>";
    }
}

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');