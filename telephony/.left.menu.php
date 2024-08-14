<?php
/**
 * @global  \CMain $APPLICATION
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
includeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/intranet/public/telephony/.left.menu.php');

if (CModule::IncludeModule('voximplant'))
{
	$aMenuLinks = [
		[
			GetMessage('SERVICES_MENU_TELEPHONY_CONNECT'),
			SITE_DIR.'telephony/index.php',
			[],
			['menu_item_id'=>'menu_telephony_start'],
			'Bitrix\Voximplant\Security\Helper::isMainMenuEnabled()'
		],
		[
			GetMessage('SERVICES_MENU_TELEPHONY_DETAIL'),
			SITE_DIR.'telephony/detail.php',
			[],
			['menu_item_id'=>'menu_telephony_detail'],
			'Bitrix\Voximplant\Security\Helper::isBalanceMenuEnabled()'
		],
	];

	if (CModule::IncludeModule('report'))
	{
		\Bitrix\Main\UI\Extension::load('report.js.analytics');
		$aMenuLinks[] = Array(
			GetMessage('SERVICES_MENU_TELEPHONY_ANALYTICS'),
			SITE_DIR.'report/telephony/?analyticBoardKey=telephony_calls_dynamics',
			Array(),
			Array('menu_item_id' => 'menu_telephony_reports'),
			''
		);
	}
}
