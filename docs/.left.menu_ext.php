<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/docs/.left.menu_ext.php");

GLOBAL $USER;
$userId = $USER->GetID();

$diskEnabled = \Bitrix\Main\Config\Option::get('disk', 'successfully_converted', false);
$diskPath = ($diskEnabled == "Y") ? SITE_DIR."company/personal/user/".$userId."/disk/path/" : SITE_DIR."company/personal/user/".$userId."/files/lib/";

$links = array(
	array(
		GetMessage("MENU_DISK_USER"),
		$diskPath,
		array(),
		array("menu_item_id" => "menu_my_disk"),
		"CBXFeatures::IsFeatureEnabled('PersonalFiles')"
	)
);

$aMenuLinks = array_merge($links, $aMenuLinks);

if ($diskEnabled == "Y")
{
	$diskPathVolume = SITE_DIR."company/personal/user/".$userId."/disk/volume/";
	$aMenuLinks[] =
		array(
			GetMessage("MENU_DISK_VOLUME"),
			$diskPathVolume,
			array(),
			array("menu_item_id" => "menu_my_disk_volume"),
			""
		);
}
