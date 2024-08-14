<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/intranet/public/crm/deal/index.php');
$APPLICATION->SetTitle(GetMessage('CRM_TITLE'));
?><?$APPLICATION->IncludeComponent(
	'bitrix:crm.deal',
	'',
	Array(
		'SEF_MODE' => 'Y',
		'PATH_TO_CONTACT' => SITE_DIR.'crm/contact/',
		'PATH_TO_CONTACT_SHOW' => SITE_DIR.'crm/contact/show/#contact_id#/',
		'PATH_TO_CONTACT_EDIT' => SITE_DIR.'crm/contact/edit/#contact_id#/',
		'PATH_TO_COMPANY' => SITE_DIR.'crm/company/',
		'PATH_TO_COMPANY_SHOW' => SITE_DIR.'crm/company/show/#company_id#/',
		'PATH_TO_COMPANY_EDIT' => SITE_DIR.'crm/company/edit/#company_id#/',
		'PATH_TO_INVOICE' => SITE_DIR.'crm/invoice/',
		'PATH_TO_INVOICE_SHOW' => SITE_DIR.'crm/invoice/show/#invoice_id#/',
		'PATH_TO_INVOICE_EDIT' => SITE_DIR.'crm/invoice/edit/#invoice_id#/',
		'PATH_TO_LEAD' => SITE_DIR.'crm/lead/',
		'PATH_TO_LEAD_SHOW' => SITE_DIR.'crm/lead/show/#lead_id#/',
		'PATH_TO_LEAD_EDIT' => SITE_DIR.'crm/lead/edit/#lead_id#/',
		'PATH_TO_LEAD_CONVERT' => SITE_DIR.'crm/lead/convert/#lead_id#/',
		'PATH_TO_USER_PROFILE' => SITE_DIR.'company/personal/user/#user_id#/',
		'PATH_TO_PRODUCT_EDIT' => SITE_DIR.'crm/product/edit/#product_id#/',
		'PATH_TO_PRODUCT_SHOW' => SITE_DIR.'crm/product/show/#product_id#/',
		'ELEMENT_ID' => $_REQUEST['deal_id'] ?? '',
		'SEF_FOLDER' => SITE_DIR.'crm/deal/',
		'SEF_URL_TEMPLATES' => Array(
			'index' => 'index.php',
			'list' => 'list/',
			'edit' => 'edit/#deal_id#/',
			'show' => 'show/#deal_id#/'
		),
		'VARIABLE_ALIASES' => Array(
			'index' => Array(),
			'list' => Array(),
			'edit' => Array(),
			'show' => Array(),
		)
	)
);?><?php

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');