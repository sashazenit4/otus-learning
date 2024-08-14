<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

$APPLICATION->IncludeComponent(
	'bitrix:sender.config.role',
	'.default',
	[
		'SEF_FOLDER' => SITE_DIR.'marketing/config/role/',
		'SEF_MODE' => 'Y',
	]
);

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');