<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->IncludeComponent("bitrix:crm.ml", ".default", array(
	'SEF_FOLDER' => SITE_DIR.'crm/ml/',
));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");