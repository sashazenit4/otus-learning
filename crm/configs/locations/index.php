<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/intranet/public/crm/configs/locations/index.php');
global $APPLICATION;

$APPLICATION->SetTitle(GetMessage('CRM_TITLE'));
?><?php
$APPLICATION->IncludeComponent(
	'bitrix:crm.config.locations',
	'.default',
	array(
		'SEF_MODE' => 'Y',
		'SEF_FOLDER' => SITE_DIR.'crm/configs/locations/'
	),
	false
);

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
