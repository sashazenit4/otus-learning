<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/crm/lead/index.php");
$APPLICATION->SetTitle(GetMessage("CRM_TITLE"));
?><?php
$APPLICATION->IncludeComponent(
	"bitrix:crm.lead",
	".default",
	[
		"SEF_MODE" => "Y",
		"PATH_TO_CONTACT_SHOW" => SITE_DIR."crm/contact/show/#contact_id#/",
		"PATH_TO_CONTACT_EDIT" => SITE_DIR."crm/contact/edit/#contact_id#/",
		"PATH_TO_COMPANY_SHOW" => SITE_DIR."crm/company/show/#company_id#/",
		"PATH_TO_COMPANY_EDIT" => SITE_DIR."crm/company/edit/#company_id#/",
		"PATH_TO_DEAL_SHOW" => SITE_DIR."crm/deal/show/#deal_id#/",
		"PATH_TO_DEAL_EDIT" => SITE_DIR."crm/deal/edit/#deal_id#/",
		"PATH_TO_USER_PROFILE" => SITE_DIR."company/personal/user/#user_id#/",
		"PATH_TO_PRODUCT_EDIT" => SITE_DIR."crm/product/edit/#product_id#/",
		"PATH_TO_PRODUCT_SHOW" => SITE_DIR."crm/product/show/#product_id#/",
		"ELEMENT_ID" => $_REQUEST["lead_id"] ?? '',
		"SEF_FOLDER" => SITE_DIR."crm/lead/",
		"SEF_URL_TEMPLATES" => [
			"index" => "index.php",
			"list" => "list/",
			"edit" => "edit/#lead_id#/",
			"show" => "show/#lead_id#/",
			"convert" => "convert/#lead_id#/",
			"import" => "import/",
			"service" => "service/",
			"dedupe" => "dedupe/"
		],
		"VARIABLE_ALIASES" => [
			"index" => [],
			"list" => [],
			"edit" => [],
			"show" => [],
			"convert" => [],
			"import" => [],
			"service" => [],
			"dedupe" => []
		]
	]
);?><?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");