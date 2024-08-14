<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/intranet/public/crm/configs/mailtemplate/index.php');
$APPLICATION->SetTitle(GetMessage('CRM_TITLE'));
$APPLICATION->IncludeComponent(
	'bitrix:crm.mail_template', 
	'', 
	array(
		'SEF_MODE' => 'Y',
		'SEF_FOLDER' => SITE_DIR.'crm/configs/mailtemplate/',
	),
	false
); 
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
