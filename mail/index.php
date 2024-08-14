<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

$APPLICATION->includeComponent(
	'bitrix:mail.client',
	'',
	array(
		'SEF_MODE' => 'Y',
		'SEF_FOLDER' => SITE_DIR.'mail/',
	)
);

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
