<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/intranet/public/crm/configs/tax/index.php');
global $APPLICATION;

if (!CModule::IncludeModule('crm'))
{
	ShowError(GetMessage('CRM_MODULE_NOT_INSTALLED'));
	return;
}

$bVatMode = CCrmTax::isVatMode();

if($bVatMode)
{
	$APPLICATION->SetTitle(GetMessage('CRM_TITLE'));
	$APPLICATION->IncludeComponent(
		'bitrix:crm.config.vat',
		'.default',
		array(
			'SEF_MODE' => 'Y',
			'SEF_FOLDER' => SITE_DIR.'crm/configs/tax/',
		),
		false
	);
}
else
{
	$APPLICATION->SetTitle(GetMessage('CRM_TITLE2'));
	$APPLICATION->IncludeComponent(
		'bitrix:crm.config.tax',
		'.default',
		array(
			'SEF_MODE' => 'Y',
			'SEF_FOLDER' => SITE_DIR.'crm/configs/tax/',
		),
		false
	);
}
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
