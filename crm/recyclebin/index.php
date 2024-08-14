<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/crm/recyclebin/index.php");
$APPLICATION->SetTitle(GetMessage("TITLE"));
$APPLICATION->IncludeComponent(
	"bitrix:crm.recyclebin.list",
	"",
	array(
		"PATH_TO_USER_PROFILE" => SITE_DIR."company/personal/user/#user_id#/"
	)
);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
