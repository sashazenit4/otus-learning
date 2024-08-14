<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/intranet/public_bitrix24/marketing/.left.menu.php');

$aMenuLinks = [];

if (!\Bitrix\Main\Loader::includeModule('sender'))
{
	return;
}

if (\Bitrix\Sender\Security\Access::current()->canViewStart())
{
	$aMenuLinks[] = [
		GetMessage('SERVICES_MENU_MARKETING_START'),
		SITE_DIR.'marketing/',
		[],
		[],
		''
	];
}

if (\Bitrix\Sender\Security\Access::current()->canViewLetters())
{
	$aMenuLinks[] = [
		GetMessage('SERVICES_MENU_MARKETING_LETTERS'),
		SITE_DIR.'marketing/letter/',
		[],
		[],
		''
	];
}

if (\Bitrix\Sender\Security\Access::current()->canViewAds())
{
	$aMenuLinks[] = [
		GetMessage('SERVICES_MENU_MARKETING_ADS'),
		SITE_DIR.'marketing/ads/',
		[],
		[],
		''
	];
}

if (\Bitrix\Sender\Security\Access::current()->canViewSegments())
{
	$aMenuLinks[] = [
		GetMessage('SERVICES_MENU_MARKETING_SEGMENTS'),
		SITE_DIR.'marketing/segment/',
		[],
		[],
		''
	];
}

if (\Bitrix\Sender\Security\Access::current()->canViewRc())
{
	$aMenuLinks[] = [
		GetMessage('SERVICES_MENU_MARKETING_RETURN_CUSTOMER'),
		SITE_DIR.'marketing/rc/',
		[],
		[],
		''
	];
}
if (
	method_exists(\Bitrix\Sender\Security\Access::current(), 'canViewToloka')
	&& \Bitrix\Sender\Security\Access::current()->canViewToloka()
)
{
	$aMenuLinks[] = [
		GetMessage('SERVICES_MENU_MARKETING_YANDEX_TOLOKA'),
		SITE_DIR.'marketing/toloka/',
		[],
		[],
		''
	];
}

$canViewTemplates = method_exists(
	\Bitrix\Sender\Security\Access::class,
	'canViewTemplates') ?
	\Bitrix\Sender\Security\Access::current()->canViewTemplates() :
	\Bitrix\Sender\Security\Access::current()->canViewLetters();


if ($canViewTemplates)
{
	$aMenuLinks[] = [
		GetMessage('SERVICES_MENU_MARKETING_TEMPLATES'),
		SITE_DIR.'marketing/template/',
		[],
		[],
		''
	];
}

if (\Bitrix\Sender\Security\Access::current()->canViewBlacklist())
{
	$aMenuLinks[] = [
		GetMessage('SERVICES_MENU_MARKETING_BLACKLIST'),
		SITE_DIR.'marketing/blacklist/',
		[],
		[],
		''
	];
}

$canViewClientList = method_exists(
	\Bitrix\Sender\Security\Access::class,
	'canViewClientList') ?
	\Bitrix\Sender\Security\Access::current()->canViewClientList() :
	\Bitrix\Sender\Security\Access::current()->canViewSegments();

if ($canViewClientList)
{
	$aMenuLinks[] = [
		GetMessage('SERVICES_MENU_MARKETING_CONTACT'),
		SITE_DIR.'marketing/contact/',
		[],
		[],
		''
	];
}

if (\Bitrix\Sender\Security\Access::current()->canModifySettings())
{
	$aMenuLinks[] = [
		GetMessage('SERVICES_MENU_MARKETING_CONFIG'),
		SITE_DIR.'marketing/config.php',
		[],
		[],
		''
	];
}

if (\Bitrix\Sender\Security\Access::current()->canModifySettings())
{
	$aMenuLinks[] = [
		GetMessage('SERVICES_MENU_MARKETING_ROLE'),
		SITE_DIR.'marketing/config/role/',
		[],
		[],
		''
	];
}