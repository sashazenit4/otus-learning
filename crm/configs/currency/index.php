<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/intranet/public/crm/configs/currency/index.php');

$APPLICATION->SetTitle(GetMessage('CRM_TITLE'));
$APPLICATION->IncludeComponent(
	'bitrix:crm.currency', 
	'.default', 
	array(
		'SEF_MODE' => 'Y',
		'SEF_FOLDER' => SITE_DIR.'crm/configs/currency/',
	),
	false
);
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');