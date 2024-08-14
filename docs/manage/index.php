<?php
/**
 * @global  \CMain $APPLICATION
 */

use Bitrix\Intranet\Integration\Wizards\Portal\Ids;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/docs/manage/index.php");
$APPLICATION->SetTitle(GetMessage("DOCS_TITLE"));
\CModule::IncludeModule('intranet');

$APPLICATION->IncludeComponent("bitrix:disk.common", ".default", Array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => SITE_DIR."docs/manage",
		"STORAGE_ID" => Ids::getDiskStorageId('MANAGE_STORAGE_ID'),
	)
);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
