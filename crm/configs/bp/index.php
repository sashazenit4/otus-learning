<?php
/**
 * @global  \CMain $APPLICATION
 */

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/intranet/public/crm/configs/bp/index.php');
CModule::IncludeModule('bizproc');


$APPLICATION->SetTitle(GetMessage('CRM_TITLE'));
?><?php
$APPLICATION->IncludeComponent(
	'bitrix:crm.config.bp',
	'',
	[
		'SEF_MODE' => 'Y',
		'SEF_FOLDER' => SITE_DIR.'crm/configs/bp/',
		'SEF_URL_TEMPLATES' => [
			'ENTITY_LIST_URL' => '',
			'FIELDS_LIST_URL' => '#entity_id#/',
			'FIELD_EDIT_URL' => '#entity_id#/edit/#bp_id#/'
		],
		'VARIABLE_ALIASES' => [
			'ENTITY_LIST_URL' => [],
			'FIELDS_LIST_URL' => [],
			'FIELD_EDIT_URL' => [],
		]
	]
);

?><?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');