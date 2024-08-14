<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

$APPLICATION->IncludeComponent(
	'bitrix:sender.blacklist', 
	'.default',
	[
		'SEF_FOLDER' => SITE_DIR.'marketing/blacklist/',
		'SEF_MODE' => 'Y',
	]
);

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');