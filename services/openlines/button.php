<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/bitrix24/public/crm/button/index.php');
$APPLICATION->SetTitle(GetMessage('CRM_TITLE'));

?>
<?$APPLICATION->IncludeComponent(
	'bitrix:imopenlines.menu.top',
	'',
	[],
	false
);?>
<?$APPLICATION->IncludeComponent(
	'bitrix:crm.button',
	'.default',
	Array(
		'HIDE_CRM_MENU' => 'Y',
		'SEF_MODE' => 'Y',
		'PATH_TO_CONTACT_SHOW' => SITE_DIR . 'crm/contact/show/#contact_id#/',
		'PATH_TO_CONTACT_EDIT' => SITE_DIR . 'crm/contact/edit/#contact_id#/',
		'PATH_TO_COMPANY_SHOW' => SITE_DIR . 'crm/company/show/#company_id#/',
		'PATH_TO_COMPANY_EDIT' => SITE_DIR . 'crm/company/edit/#company_id#/',
		'PATH_TO_DEAL_SHOW' => SITE_DIR . 'crm/deal/show/#deal_id#/',
		'PATH_TO_DEAL_EDIT' => SITE_DIR . 'crm/deal/edit/#deal_id#/',
		'PATH_TO_USER_PROFILE' => SITE_DIR . 'company/personal/user/#user_id#/',
		'ELEMENT_ID' => $_REQUEST['id'],
		'SEF_FOLDER' => SITE_DIR . 'crm/button/',
		'SEF_URL_TEMPLATES' => Array(
			'list' => 'list/',
			'edit' => 'edit/#id#/',
		),
		'VARIABLE_ALIASES' => Array(
			'list' => Array(),
			'edit' => Array(),
		)
	)
);?><?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>