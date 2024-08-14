<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/intranet/public/search/.left.menu.php');

$aMenuLinks = [
	[
		GetMessage('SEARCH_MAIN'),
		SITE_DIR.'search/index.php', 
		[], 
		[], 
		''
	],
	[
		GetMessage('SEARCH_MAP'),
		SITE_DIR.'search/map.php', 
		[], 
		[], 
		''
	]
];
