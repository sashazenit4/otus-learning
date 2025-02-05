<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
/**
 * @var CMain $APPLICATION
 */

\Bitrix\Main\Loader::includeModule('iblock');
$select = [
    'NAME',
    'PROCEDURES_ID.ELEMENT.NAME',
    'PROCEDURES_ID.ELEMENT.COLOR.VALUE',
];

function querySelection($iblock, $select)
{
    return $iblock::query()
    ->setSelect($select)
    ->fetchCollection();
}

function getListSelection($iblock, $select)
{
    return $iblock::getList([
        'select' => $select,
    ])->fetchCollection();
}

$iblock = '\Bitrix\Iblock\Elements\ElementDoctorsTable';

$result = querySelection($iblock, $select); # оба варианта правильные
$result = getListSelection($iblock, $select); # оба варианта правильные

$doctors = [];

foreach ($result as $doctor) {
    $doctorName = $doctor->getName();
    $procedures = $doctor->getProceduresId();

    foreach ($procedures as $procedure) {
        $procedureName = $procedure->getElement()->getName();
        $colors= $procedure->getElement()->getColor();

        foreach ($colors as $color) {
            $doctors[$doctorName][$procedureName][] = $color->getValue();
        }
    }
}

echo '<pre>'; var_dump($doctors); echo '</pre>';

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');