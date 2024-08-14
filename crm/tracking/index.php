<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->IncludeComponent("bitrix:crm.tracking", ".default", array(
	'SEF_FOLDER' => SITE_DIR.'crm/tracking/',
));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");