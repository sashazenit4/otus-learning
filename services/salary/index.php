<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/services/salary/index.php");
$APPLICATION->SetTitle(GetMessage("SERVICES_TITLE"));?>
<?$APPLICATION->IncludeComponent("bitrix:payroll.1c", ".default", array(
	"ORG_LIST" => array(
		0 => GetMessage("SERVICES_ORG_LIST"),
	),
	"PR_TIMEOUT" => "25",
	"PR_NAMESPACE" => "http://www.1c-bitrix.ru",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600",
	"PR_URL_0" => "",
	"PR_PORT_0" => "80",
	"PR_LOGIN_0" => "",
	"PR_PASSWORD_0" => ""
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>