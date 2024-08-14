<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/intranet/public/company/leaders.php');
$APPLICATION->SetTitle(GetMessage('COMPANY_TITLE'));
?><?php
$APPLICATION->IncludeComponent(
	'bitrix:intranet.structure.honour', '.default', [
		'STRUCTURE_PAGE' => SITE_DIR.'company/structure.php',
		'PM_URL' => SITE_DIR.'company/personal/messages/chat/#USER_ID#/',
		'PATH_TO_VIDEO_CALL' => SITE_DIR.'company/personal/video/#USER_ID#/',
		'PATH_TO_CONPANY_DEPARTMENT' => SITE_DIR.'company/structure.php?set_filter_structure=Y&structure_UF_DEPARTMENT=#ID#',
		'STRUCTURE_FILTER' => 'structure',
		'NUM_USERS' => '25',
		'USER_PROPERTY' => [
			0 => 'PERSONAL_PHONE',
			1 => 'UF_DEPARTMENT',
			2 => 'UF_PHONE_INNER',
			3 => 'UF_SKYPE',
			4 => 'PERSONAL_PHOTO',
		],
	]
);
?><?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');