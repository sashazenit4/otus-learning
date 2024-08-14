<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

use Bitrix\Intranet\Site\Sections\AutomationSection;
use Bitrix\Main\Loader;

$GLOBALS['APPLICATION']->setPageProperty('topMenuSectionDir', SITE_DIR . 'automation/');

if (!Loader::includeModule('intranet'))
{
	return;
}

$menuItems = [];
$items = AutomationSection::getItems();
foreach ($items as $item)
{
	if ($item['available'])
	{
		$menuData = $item['menuData'] ?? [];
		unset($menuData['counter_id']);

		$menuItems[] = [
			$item['title'] ?? '',
			$item['url'] ?? '',
			$item['extraUrls'] ?? [],
			$menuData,
			'',
		];
	}
}

$aMenuLinks = array_merge($menuItems, $aMenuLinks);