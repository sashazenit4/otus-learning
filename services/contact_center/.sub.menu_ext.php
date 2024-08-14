<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

$result = $GLOBALS['APPLICATION']->includeComponent(
	'bitrix:intranet.contact_center.menu.top',
	'',
	[
		'COMPONENT_BASE_DIR' => SITE_DIR . 'services/contact_center/',
		'MENU_MODE' => 'Y',
	],
	false
);

$aMenuLinks = is_array($result) && isset($result['ITEMS']) ? $result['ITEMS'] : [];