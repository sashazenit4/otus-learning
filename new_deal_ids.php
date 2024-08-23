<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
/**
 * @var CMain $APPLICATION
 */

$APPLICATION->SetTitle('Идентификаторы новых сделок');
$APPLICATION->IncludeComponent('aholin:deal.grid', '', [
    'STAGE_ID' => 'NEW',
]);
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');