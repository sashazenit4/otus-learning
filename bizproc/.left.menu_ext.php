<?php

use Bitrix\Main\IO\File;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

$automationMenu = $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'automation/.left.menu.php';
if (File::isFileExists($automationMenu))
{
	include($automationMenu);
}

$automationMenuExt = $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'automation/.left.menu_ext.php';
if (File::isFileExists($automationMenuExt))
{
	include($automationMenuExt);
}