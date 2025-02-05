<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

$APPLICATION->includeComponent(
	'bitrix:sign.start',
	'',
	array(
		'SEF_MODE' => 'Y',
		'SEF_FOLDER' => SITE_DIR.'sign/'
	),
	null
);

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');