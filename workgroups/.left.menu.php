<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/intranet/public/workgroups/.left.menu.php');

$aMenuLinks = [
	[
		GetMessage('WORKGROUPS_MENU_GROUPS'),
		SITE_DIR.'workgroups/index.php?filter_my=Y',
		[], 
		[], 
		''
	]
];
