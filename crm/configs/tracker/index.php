<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/bitrix24/public/crm/configs/tracker/index.php");
$APPLICATION->SetTitle(GetMessage("TITLE"));

$APPLICATION->IncludeComponent(
		"bitrix:crm.config.tracker",
		".default"
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>