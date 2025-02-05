<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->IncludeComponent(
	"bitrix:call.conference.center",
	".default", array(
		'SEF_FOLDER' => SITE_DIR.'conference/',
	)
);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");