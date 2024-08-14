<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/intranet/public/crm/configs/fields/index.php');
$APPLICATION->SetTitle(GetMessage('CRM_TITLE'));

?><?php
$APPLICATION->IncludeComponent(
	'bitrix:crm.config.fields',
	'',
	[
		'SEF_MODE' => 'Y',
		'CACHE_TYPE' => 'A',
		'CACHE_TIME' => '3600',
		'CACHE_NOTES' => '',
		'SEF_FOLDER' => SITE_DIR.'crm/configs/fields/',
		'SEF_URL_TEMPLATES' => [
			'ENTITY_LIST_URL' => '',
			'FIELDS_LIST_URL' => '#entity_id#/',
			'FIELD_EDIT_URL' => '#entity_id#/edit/#field_id#/'
		],
		'VARIABLE_ALIASES' => [
			'ENTITY_LIST_URL' => [],
			'FIELDS_LIST_URL' => [],
			'FIELD_EDIT_URL' => [],
		]
	]
);?><?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
