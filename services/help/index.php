<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/services/help/index.php");
$APPLICATION->SetTitle(GetMessage("SERVICES_TITLE"));
?>
<ul style="padding-left: 23px;display: block; list-style-type: disc;" >
	<li style="color: #3F729C;height: 22px;list-style: none outside none;margin-bottom: 26px;overflow: hidden;">- <a href="/services/help/extranet.php" style="color: #3F729C;font-family: Arial,sans-serif;font-size: 18px;"><?=GetMessage("SERVICES_MENU_EXTRANET")?></a></li>
	<li style="color: #3F729C;height: 22px;list-style: none outside none;margin-bottom: 26px;overflow: hidden;">- <a href="/services/help/absence_help.php" style="color: #3F729C;font-family: Arial,sans-serif;font-size: 18px;"><?=GetMessage("SERVICES_MENU_ABSENCE_HELP")?></a></li>
	<li style="color: #3F729C;height: 22px;list-style: none outside none;margin-bottom: 26px;overflow: hidden;">- <a href="/services/help/outlook.php" style="color: #3F729C;font-family: Arial,sans-serif;font-size: 18px;"><?=GetMessage("SERVICES_MENU_OUTLOOK")?></a></li>
	<li style="color: #3F729C;height: 22px;list-style: none outside none;margin-bottom: 26px;overflow: hidden;">- <a href="/services/help/novice.php" style="color: #3F729C;font-family: Arial,sans-serif;font-size: 18px;"><?=GetMessage("SERVICES_MENU_NOVICE")?></a></li>
	<li style="color: #3F729C;height: 22px;list-style: none outside none;margin-bottom: 26px;overflow: hidden;">- <a href="/services/help/bp_help.php" style="color: #3F729C;font-family: Arial,sans-serif;font-size: 18px;"><?=GetMessage("SERVICES_MENU_BP")?></a></li>
	<li style="color: #3F729C;height: 22px;list-style: none outside none;margin-bottom: 26px;overflow: hidden;">- <a href="/services/help/xmpp.php" style="color: #3F729C;font-family: Arial,sans-serif;font-size: 18px;"><?=GetMessage("SERVICES_MENU_XMPP")?></a></li>
</ul>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>