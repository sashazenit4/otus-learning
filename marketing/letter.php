<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

$APPLICATION->IncludeComponent(
	'bitrix:sender.letter', 
	'.default',
	array(
		'SEF_FOLDER' => SITE_DIR.'marketing/letter/',
		'SEF_MODE' => 'Y',
		'PATH_TO_SEGMENT_ADD' => SITE_DIR.'marketing/segment/edit/0/',
		'PATH_TO_SEGMENT_EDIT' => SITE_DIR.'marketing/segment/edit/#id#/',
	)
);

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');