<?php
/**
 * @global  \CMain $APPLICATION
 */

use Bitrix\Intranet\Integration\Wizards\Portal\Ids;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/docs/shared/index.php");
$APPLICATION->SetTitle(GetMessage("DOCS_TITLE"));
$APPLICATION->AddChainItem($APPLICATION->GetTitle(), "/docs/shared/");
\CModule::IncludeModule('intranet');
?><?php
$APPLICATION->IncludeComponent("bitrix:disk.common", ".default", [
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => SITE_DIR."docs/shared",
		"STORAGE_ID" => Ids::getDiskStorageId('SHARED_STORAGE_ID'),
	]
);?><?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");