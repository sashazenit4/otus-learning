<?php

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

Loader::includeModule('highloadblock');

$arElementFields = array(
    'UF_COLOR_NAME' => 'Синий',
    'UF_COLOR_HEX' => '#0000FF',
);

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

$addResult = $entityClassName::add($arElementFields);

$ID = $addResult->getID();
$bSuccess = $addResult->isSuccess();
if ($bSuccess)
    echo "Highload element {$ID} was added!";
else {
    $arErrors = $addResult->getErrorMessages();
    foreach ($arErrors as $error) {
        echo "ERROR: " . $error . "<br>";
    }
}

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');