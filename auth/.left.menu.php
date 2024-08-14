<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/auth/.left.menu.php");

$aMenuLinks = Array(
	Array(
		GetMessage("AUTH_LOGIN"),
		SITE_DIR."auth/index.php?login=yes",
		Array(), 
		Array(), 
		"" 
	),
	Array(
		GetMessage("AUTH_REG"),
		SITE_DIR."auth/index.php?register=yes",
		Array(), 
		Array(), 
		"COption::GetOptionString(\"main\", \"new_user_registration\") == \"Y\"" 
	),
	Array(
		GetMessage("AUTH_FORGOT_PASS"),
		SITE_DIR."auth/index.php?forgot_password=yes",
		Array(), 
		Array(), 
		"" 
	)
);
?>