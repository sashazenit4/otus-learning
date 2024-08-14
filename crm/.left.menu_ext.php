<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

if (SITE_TEMPLATE_ID === "bitrix24")
{
	return;
}

include(__DIR__."/menu/.left.menu_ext.php");