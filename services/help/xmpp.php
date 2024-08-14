<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/services/help/xmpp.php");
$APPLICATION->SetTitle(GetMessage("SERVICES_TITLE"));

?><script type="text/javascript" src="/bitrix/templates/learning/js/imgshw.js"></script>
<?=GetMessage("SERVICES_INFO", array("#SITE#" => SITE_DIR));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
