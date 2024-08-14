<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/intranet/public/search/index.php');
$APPLICATION->SetTitle(GetMessage('SEARCH_TITLE'));

?><?php $APPLICATION->IncludeComponent(
	'bitrix:search.page',
	'icons',
	[
		'AJAX_MODE' => 'N', 
		'RESTART' => 'N', 
		'CHECK_DATES' => 'N', 
		'USE_TITLE_RANK' => 'N', 
		'arrWHERE' => ['intranet', 'iblock_news','iblock_library','blog'], 
		'arrFILTER' => [], 
		'SHOW_WHERE' => 'Y', 
		'PAGE_RESULT_COUNT' => '50', 
		'CACHE_TYPE' => 'A', 
		'CACHE_TIME' => '36000000', 
		'PAGER_TITLE' => GetMessage('SEARCH_RESULT'),
		'PAGER_SHOW_ALWAYS' => 'N', 
		'PAGER_TEMPLATE' => '', 
		'SHOW_RATING' => '',
		'RATING_TYPE' => '',
		'PATH_TO_USER_PROFILE' => SITE_DIR.'company/personal/user/#user_id#/',
		'PATH_TO_USER_EDIT' => SITE_DIR.'company/personal/user/#user_id#/edit/',
		'AJAX_OPTION_SHADOW' => 'Y', 
		'AJAX_OPTION_JUMP' => 'N', 
		'AJAX_OPTION_STYLE' => 'Y', 
		'AJAX_OPTION_HISTORY' => 'N'
	]
);
?><?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
