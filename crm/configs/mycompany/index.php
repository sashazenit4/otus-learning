<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/intranet/public/crm/company/index.php');
$APPLICATION->SetTitle(GetMessage('CRM_TITLE'));
?><?php
$APPLICATION->IncludeComponent(
	'bitrix:crm.company',
	'',
	[
		'SEF_MODE' => 'Y',
		'PATH_TO_LEAD_SHOW' => SITE_DIR.'crm/lead/show/#lead_id#/',
		'PATH_TO_LEAD_EDIT' => SITE_DIR.'crm/lead/edit/#lead_id#/',
		'PATH_TO_LEAD_CONVERT' => SITE_DIR.'crm/lead/convert/#lead_id#/',
		'PATH_TO_CONTACT_SHOW' => SITE_DIR.'crm/contact/show/#contact_id#/',
		'PATH_TO_CONTACT_EDIT' => SITE_DIR.'crm/contact/edit/#contact_id#/',
		'PATH_TO_DEAL_SHOW' => SITE_DIR.'crm/deal/show/#deal_id#/',
		'PATH_TO_DEAL_EDIT' => SITE_DIR.'crm/deal/edit/#deal_id#/',
		'PATH_TO_INVOICE_SHOW' => SITE_DIR.'crm/invoice/show/#invoice_id#/',
		'PATH_TO_INVOICE_EDIT' => SITE_DIR.'crm/invoice/edit/#invoice_id#/',
		'PATH_TO_USER_PROFILE' => '/company/personal/user/#user_id#/',
		'ELEMENT_ID' => $_REQUEST['company_id'] ?? '',
		'SEF_FOLDER' => SITE_DIR.'crm/configs/mycompany/',
		'SEF_URL_TEMPLATES' => [
			'index' => 'index.php',
			'list' => 'list/',
			'import' => 'import/',
			'edit' => 'edit/#company_id#/',
			'show' => 'show/#company_id#/',
			'dedupe' => 'dedupe/'
		],
		'VARIABLE_ALIASES' => [
			'index' => [],
			'list' => [],
			'import' => [],
			'edit' => [],
			'show' => [],
			'dedupe' => []
		],
		'MYCOMPANY_MODE' => 'Y'
	]
);?><?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');

