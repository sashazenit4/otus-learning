<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/crm/events/index.php");
$APPLICATION->SetTitle(GetMessage("CRM_TITLE"));
?><?php
$APPLICATION->IncludeComponent(
	"bitrix:crm.event.view",
	"",
	[
		"ENTITY_ID" => "",
		"EVENT_COUNT" => "20",
		"EVENT_ENTITY_LINK" => "Y",
		"PATH_TO_DEAL_SHOW" => SITE_DIR."crm/deal/show/#deal_id#/",
		"PATH_TO_QUOTE_SHOW" => SITE_DIR."crm/quote/show/#quote_id#/",
		"PATH_TO_CONTACT_SHOW" => SITE_DIR."crm/contact/show/#contact_id#/",
		"PATH_TO_COMPANY_SHOW" => SITE_DIR."crm/company/show/#company_id#/",
		"PATH_TO_LEAD_SHOW" => SITE_DIR."crm/lead/show/#lead_id#/",
		"PATH_TO_USER_PROFILE" => SITE_DIR."company/personal/user/#user_id#/"
	]
);?><?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");