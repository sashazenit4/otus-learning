<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/crm/quote/index.php");
$APPLICATION->SetTitle(GetMessage("CRM_TITLE"));
?><?php
$APPLICATION->IncludeComponent(
	"bitrix:crm.quote",
	"",
	[
		"SEF_MODE" => "Y",
		"PATH_TO_CONTACT_SHOW" => SITE_DIR."crm/contact/show/#contact_id#/",
		"PATH_TO_CONTACT_EDIT" => SITE_DIR."crm/contact/edit/#contact_id#/",
		"PATH_TO_COMPANY_SHOW" => SITE_DIR."crm/company/show/#company_id#/",
		"PATH_TO_COMPANY_EDIT" => SITE_DIR."crm/company/edit/#company_id#/",
		"PATH_TO_DEAL_SHOW" => SITE_DIR."crm/deal/show/#deal_id#/",
		"PATH_TO_DEAL_EDIT" => SITE_DIR."crm/deal/edit/#deal_id#/",
		"PATH_TO_INVOICE_SHOW" => SITE_DIR."crm/invoice/show/#invoice_id#/",
		"PATH_TO_INVOICE_EDIT" => SITE_DIR."crm/invoice/edit/#invoice_id#/",
		"PATH_TO_LEAD_SHOW" => SITE_DIR."crm/lead/show/#lead_id#/",
		"PATH_TO_LEAD_EDIT" => SITE_DIR."crm/lead/edit/#lead_id#/",
		"PATH_TO_LEAD_CONVERT" => SITE_DIR."crm/lead/convert/#lead_id#/",
		"PATH_TO_PRODUCT_EDIT" => SITE_DIR."crm/product/edit/#product_id#/",
		"PATH_TO_PRODUCT_SHOW" => SITE_DIR."crm/product/show/#product_id#/",
		"PATH_TO_USER_PROFILE" => "/company/personal/user/#user_id#/",
		"ELEMENT_ID" => $_REQUEST["quote_id"] ?? '',
		"SEF_FOLDER" => SITE_DIR."crm/quote/",
		"SEF_URL_TEMPLATES" => [
			"index" => "index.php",
			"list" => "list/",
			"edit" => "edit/#quote_id#/",
			"show" => "show/#quote_id#/"
		],
		"VARIABLE_ALIASES" => [
			"index" => [],
			"list" => [],
			"edit" => [],
			"show" => [],
		]
	]
);?><?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");