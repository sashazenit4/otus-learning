<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
/**
 * @var CMain $APPLICATION
 */

\Bitrix\Main\Loader::includeModule('iblock');
$select = [
    'NAME',
    'PROCEDURE_NAME' => 'PROCEDURES_ID.ELEMENT.NAME',
    'PROCEDURE_COLOR' => 'PROCEDURES_ID.ELEMENT.COLOR.VALUE',
];

$iblock = '\Bitrix\Iblock\Elements\ElementDoctorsTable';

function querySelection($iblock, $select)
{
    return $iblock::query()
        ->setSelect($select)
        ->fetchAll();
}

function getListSelection($iblock, $select)
{
    return $iblock::getList([
        'select' => $select,
    ])->fetchAll();
}

$iblock = '\Bitrix\Iblock\Elements\ElementDoctorsTable';

$result = querySelection($iblock, $select); # оба варианта правильные
$result = getListSelection($iblock, $select); # оба варианта правильные

$doctors = [];

foreach ($result as $doctor) {
    if ($doctor['PROCEDURE_COLOR']) {
        $doctors[$doctor['NAME']][$doctor['PROCEDURE_NAME']][] = $doctor['PROCEDURE_COLOR'];
    }
}

echo '<pre>'; var_dump($doctors); echo '</pre>';

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');