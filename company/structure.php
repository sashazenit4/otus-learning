<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/intranet/public/company/structure.php');

$APPLICATION->SetTitle(GetMessage('COMPANY_TITLE'));
$APPLICATION->AddChainItem(GetMessage('COMPANY_TITLE'), 'structure.php');
?><?php
$APPLICATION->IncludeComponent(
	'bitrix:intranet.structure', 
	'.default', 
	[
		'SEARCH_URL' => 'index.php',
		'PM_URL' => SITE_DIR.'company/personal/messages/chat/#USER_ID#/',
		'USERS_PER_PAGE' => '25',
		'FILTER_SECTION_CURONLY' => 'Y',
		'SHOW_ERROR_ON_NULL' => 'N',
		'NAV_TITLE' => GetMessage('COMPANY_NAV_TITLE'),
		'SHOW_NAV_TOP' => 'Y',
		'SHOW_NAV_BOTTOM' => 'Y',
		'SHOW_UNFILTERED_LIST' => 'N',
		'AJAX_MODE' => 'N',
		'AJAX_OPTION_SHADOW' => 'N',
		'AJAX_OPTION_JUMP' => 'N',
		'AJAX_OPTION_STYLE' => 'Y',
		'AJAX_OPTION_HISTORY' => 'Y',
		'FILTER_1C_USERS' => 'N',
		'FILTER_NAME' => 'structure',
		'SHOW_FROM_ROOT' => 'N',
		'MAX_DEPTH' => '2',
		'MAX_DEPTH_FIRST' => '5',
		'COLUMNS' => '2',
		'COLUMNS_FIRST' => '2',
		'SHOW_SECTION_INFO' => 'Y',
		'USER_PROPERTY' => [
			0 => 'EMAIL',
			1 => 'PERSONAL_PHONE',
			2 => 'PERSONAL_FAX',
			3 => 'UF_PHONE_INNER',
			4 => 'UF_SKYPE',
			5 => 'PERSONAL_PHOTO',
		],
	]
); ?><?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');