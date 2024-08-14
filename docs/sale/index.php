<?php
/**
 * @global  \CMain $APPLICATION
 */

use Bitrix\Intranet\Integration\Wizards\Portal\Ids;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/docs/sale/index.php");
$APPLICATION->SetTitle(GetMessage("DOCS_TITLE"));
\CModule::IncludeModule('intranet');

$APPLICATION->IncludeComponent("bitrix:disk.common", ".default", Array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => SITE_DIR."docs/sale",
		"STORAGE_ID" => Ids::getDiskStorageId('SALE_STORAGE_ID'),
	)
);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
