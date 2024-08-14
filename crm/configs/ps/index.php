<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/intranet/public/crm/configs/ps/index.php');
global $APPLICATION;

$APPLICATION->SetTitle(GetMessage('CRM_TITLE'));
$APPLICATION->IncludeComponent(
	'bitrix:crm.config.ps',
	'.default',
	array(
		'SEF_MODE' => 'Y',
		'SEF_FOLDER' => SITE_DIR.'crm/configs/ps/'
	),
	false
);

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
