<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/configs/index.php");
$APPLICATION->SetTitle(GetMessage("CONFIG_TITLE"));

$APPLICATION->IncludeComponent("bitrix:intranet.configs", "", array());

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>