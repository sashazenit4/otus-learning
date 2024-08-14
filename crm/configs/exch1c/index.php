<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/crm/configs/exch1c/index.php");
$APPLICATION->SetTitle(GetMessage("CRM_TITLE"));
?>
<?
$APPLICATION->IncludeComponent(
	'bitrix:ui.sidepanel.wrapper',
	'',
	[
		'POPUP_COMPONENT_NAME' => 'bitrix:crm.config.exch1c',
		'POPUP_COMPONENT_TEMPLATE_NAME' => '',
		'POPUP_COMPONENT_PARAMS' => [
			"SEF_MODE" => "Y",
			"SEF_FOLDER" => "/crm/configs/exch1c/",
			"PATH_TO_CONFIGS_INDEX" => "/crm/configs/"
		]
	]
);
?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>