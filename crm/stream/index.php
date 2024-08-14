<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
global $APPLICATION;
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/crm/stream/index.php");
$APPLICATION->SetTitle(GetMessage("CRM_TITLE"));

$bodyClass = $APPLICATION->GetPageProperty("BodyClass");
$APPLICATION->SetPageProperty("BodyClass", ($bodyClass ? $bodyClass." " : "")."page-one-column");

if(CModule::IncludeModule("crm") && CCrmPerms::IsAccessEnabled()):

	$currentUserPerms = CCrmPerms::GetCurrentUserPermissions();
	$canEdit = CCrmLead::CheckUpdatePermission(0, $currentUserPerms)
		|| CCrmContact::CheckUpdatePermission(0, $currentUserPerms)
		|| CCrmCompany::CheckUpdatePermission(0, $currentUserPerms)
		|| CCrmDeal::CheckUpdatePermission(0, $currentUserPerms);

	$APPLICATION->IncludeComponent(
		"bitrix:crm.control_panel",
		"",
		array(
			"ID" => "STREAM",
			"ACTIVE_ITEM_ID" => "STREAM",
			"PATH_TO_COMPANY_LIST" => SITE_DIR."crm/company/",
			"PATH_TO_COMPANY_EDIT" => SITE_DIR."crm/company/edit/#company_id#/",
			"PATH_TO_CONTACT_LIST" => SITE_DIR."crm/contact/",
			"PATH_TO_CONTACT_EDIT" => SITE_DIR."crm/contact/edit/#contact_id#/",
			"PATH_TO_DEAL_LIST" => SITE_DIR."crm/deal/",
			"PATH_TO_DEAL_EDIT" => SITE_DIR."crm/deal/edit/#deal_id#/",
			"PATH_TO_QUOTE_LIST" => SITE_DIR."crm/quote/",
			"PATH_TO_QUOTE_EDIT" => SITE_DIR."crm/quote/edit/#quote_id#/",
			"PATH_TO_INVOICE_LIST" => SITE_DIR."crm/invoice/",
			"PATH_TO_INVOICE_EDIT" => SITE_DIR."crm/invoice/edit/#invoice_id#/",
			"PATH_TO_LEAD_LIST" => SITE_DIR."crm/lead/",
			"PATH_TO_LEAD_EDIT" => SITE_DIR."crm/lead/edit/#lead_id#/",
			"PATH_TO_REPORT_LIST" => SITE_DIR."crm/reports/report/",
			"PATH_TO_DEAL_FUNNEL" => SITE_DIR."crm/reports/",
			"PATH_TO_EVENT_LIST" => SITE_DIR."crm/events/",
			"PATH_TO_PRODUCT_LIST" => SITE_DIR."crm/product/",
			"PATH_TO_SETTINGS" => SITE_DIR."crm/configs/",
			"PATH_TO_SEARCH_PAGE" => SITE_DIR."search/index.php?where=crm"
		)
	);

	// --> IMPORT RESPONSIBILITY SUBSCRIPTIONS
	$currentUserID = CCrmSecurityHelper::GetCurrentUserID();
	if($currentUserID > 0)
	{
		CCrmSonetSubscription::EnsureAllResponsibilityImported($currentUserID);
	}
	// <-- IMPORT RESPONSIBILITY SUBSCRIPTIONS
	$APPLICATION->IncludeComponent("bitrix:crm.entity.livefeed",
		"",
		array(
			"CAN_EDIT" => $canEdit,
			"FORM_ID" => "",
			"PATH_TO_USER_PROFILE" => SITE_DIR."company/personal/user/#user_id#/",
			"PATH_TO_GROUP" => SITE_DIR."workgroups/group/#group_id#/",
			"PATH_TO_CONPANY_DEPARTMENT" => SITE_DIR."company/structure.php?set_filter_structure=Y&structure_UF_DEPARTMENT=#ID#"
		),
		null,
		array("HIDE_ICONS" => "Y")
	);
endif;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
