<?php
/**
 * @global  \CMain $APPLICATION
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/services/.left.menu.php");

$aMenuLinks = [
	[
		GetMessage("SERVICES_MENU_MEETING_ROOM"),
		SITE_DIR."services/index.php",
		[SITE_DIR."services/res_c.php"],
		[],
		"CBXFeatures::IsFeatureEnabled('MeetingRoomBookingSystem')"
	],
	[
		GetMessage("SERVICES_MENU_LISTS"),
		SITE_DIR."services/lists/",
		[],
		[],
		"CBXFeatures::IsFeatureEnabled('Lists')"
	],
	[
		GetMessage("SERVICES_MENU_CONTACT_CENTER"),
		SITE_DIR."services/contact_center/",
		[],
		[],
		""
	],
	[
		GetMessage("SERVICES_MENU_EVENTLIST"),
		SITE_DIR."services/event_list.php",
		[],
		[],
		"CBXFeatures::IsFeatureEnabled('EventList')"
	],
	[
		GetMessage("SERVICES_MENU_SALARY"),
		SITE_DIR."services/salary/",
		[],
		[],
		"LANGUAGE_ID == 'ru' && CBXFeatures::IsFeatureEnabled('Salary')"
	],
	[
		GetMessage("SERVICES_MENU_TELEPHONY"),
		SITE_DIR."services/telephony/",
		[],
		[],
		'CModule::IncludeModule("voximplant") && SITE_TEMPLATE_ID !== "bitrix24" && Bitrix\Voximplant\Security\Helper::isMainMenuEnabled()'
	]
];
