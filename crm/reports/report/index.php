<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/crm/reports/report/index.php");
$APPLICATION->SetTitle(GetMessage("CRM_TITLE"));
?><?php
$APPLICATION->IncludeComponent(
	"bitrix:crm.report",
	"",
	[
		"SEF_MODE" => "Y",
		"REPORT_ID" => $_REQUEST["report_id"] ?? '',
		"SEF_FOLDER" => SITE_DIR."crm/reports/report/",
		"SEF_URL_TEMPLATES" => [
			"index" => "index.php",
			"report" => "report/",
			"construct" => "construct/#report_id#/#action#/",
			"show" => "view/#report_id#/"
		],
		"VARIABLE_ALIASES" => [
			"index" => [],
			"report" => [],
			"construct" => [],
			"show" => []
		]
	]
);
?><?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
