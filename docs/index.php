<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
if(!\Bitrix\Main\Loader::includeModule('disk'))
	return;
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/docs/index.php");
$APPLICATION->SetTitle(GetMessage("DOCS_TITLE"));
?><?php
$APPLICATION->IncludeComponent(
	"bitrix:disk.aggregator",
	"",
	Array(
		"SEF_MODE" => "Y",
		"CACHE_TIME" => 3600,
		"SEF_FOLDER" => SITE_DIR."docs/all",
	),
false
);?><?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
