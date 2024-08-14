<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/crm/webform/index.php");
$APPLICATION->SetTitle(GetMessage("CRM_TITLE"));
?><?php
$APPLICATION->IncludeComponent(
	"bitrix:crm.webform",
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
		"PATH_TO_WEB_FORM_FILL" => "/pub/form/#form_code#/#form_sec#/",
		"ELEMENT_ID" => $_REQUEST["id"] ?? '',
		"SEF_FOLDER" => SITE_DIR."crm/webform/",
		"SEF_URL_TEMPLATES" => [
			"list" => "list/",
			"edit" => "edit/#id#/",
		],
		"VARIABLE_ALIASES" => [
			"list" => [],
			"edit" => [],
		]
	]
);?><?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");