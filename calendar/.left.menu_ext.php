<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/calendar/.left.menu_ext.php");

$userId = $USER->getId();

$aMenuLinks = array(
	array(
		GetMessage("MENU_CALENDAR_USER"),
		SITE_DIR."company/personal/user/".$userId."/calendar/",
		array(),
		array(
			"menu_item_id" => "menu_my_calendar", 
			"counter_id" => "calendar"
		),
		"CBXFeatures::IsFeatureEnabled('Calendar')"
	),
	array(
		GetMessage("MENU_CALENDAR_COMPANY"),
		SITE_DIR."calendar/",
		array(),
		array(
			"menu_item_id" => "menu_company_calendar"
		),
		"CBXFeatures::IsFeatureEnabled('CompanyCalendar')"
	),
	array(
		GetMessage("MENU_CALENDAR_ROOMS"),
		SITE_DIR."calendar/rooms/",
		array(),
		array(
			"menu_item_id" => "menu_rooms"
		),
		"CBXFeatures::IsFeatureEnabled('Calendar')"
	)
);