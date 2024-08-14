<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/intranet/public/crm/configs/perms/index.php');
$APPLICATION->SetTitle(GetMessage('CRM_TITLE'));
$APPLICATION->IncludeComponent(
	'bitrix:crm.config.perms',
	'',
	[
		'SEF_MODE' => 'Y',
		'SEF_FOLDER' => SITE_DIR.'crm/configs/perms/',
		'SEF_URL_TEMPLATES' => [
			'PATH_TO_ENTITY_LIST' => '',
			'PATH_TO_ROLE_EDIT' => '#role_id#/edit/'
		],
		'VARIABLE_ALIASES' => [
			'PATH_TO_ENTITY_LIST' => [],
			'PATH_TO_ROLE_EDIT' => [],
		]
	]
);

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');