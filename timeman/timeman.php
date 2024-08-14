<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$APPLICATION->SetPageProperty("HIDE_SIDEBAR", "Y");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_after.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/timeman/timeman.php");

$APPLICATION->SetTitle(GetMessage("COMPANY_TITLE"));
?> <?
if (\Bitrix\Main\Loader::includeModule('timeman'))
{
	$APPLICATION->IncludeComponent(
		"bitrix:ui.sidepanel.wrapper",
		"",
		array(
			"POPUP_COMPONENT_NAME" => "bitrix:timeman.worktime.stats",
			"POPUP_COMPONENT_TEMPLATE_NAME" => "",
			"POPUP_COMPONENT_PARAMS" => array(
				'SCHEDULE_ID' => $_REQUEST['SCHEDULE_ID'] ?? '',
			),
			"USE_UI_TOOLBAR" => "Y"
		)
	);
}
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>