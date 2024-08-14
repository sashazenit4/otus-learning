<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/company/outlook_inc.php");

CModule::IncludeModule("intranet");
CModule::IncludeModule("iblock");
?>
<table style="margin-top:7px;">
<tr><td><li><a href="javascript:<?echo htmlspecialcharsbx(CIntranetUtils::GetStsSyncURL(array('LINK_URL' => SITE_DIR.'company/'), 'contacts'))?>"><?=GetMessage("COMPANY_CONTACTS")?></a></td></tr>
<tr><td><li><a href="javascript:<?echo htmlspecialcharsbx(CIntranetUtils::GetStsSyncURL(array('LINK_URL' => '/'.$USER->GetID().'/'), 'tasks'))?>"><?=GetMessage("COMPANY_TASKS")?></a></td></tr>
<?

if(COption::GetOptionInt("intranet", 'iblock_calendar', 0)>0):
	$dbRs = CIBlockSection::GetList(Array(), Array("IBLOCK_ID"=>COption::GetOptionInt("intranet", 'iblock_calendar', 0), 'CREATED_BY'=>$USER->GetID()));
	if($arRs = $dbRs->Fetch()):
		$dbRs2 = CIBlockSection::GetList(Array(), Array('SECTION_ID'=>$arRs["ID"]));
		while($arRs2 = $dbRs2->GetNext()):
		?>
		<tr><td><li><a href="javascript:<?echo htmlspecialcharsbx(CIntranetUtils::GetStsSyncURL(array('ID' => $arRs2["ID"], 'LINK_URL' => 'company/personal/user/'.$USER->GetID().'/calendar/'), 'calendar'))?>"><?=GetMessage("COMPANY_CONNECT")?> <?=$arRs2["NAME"]?></a></td></tr>
		<?endwhile?>
	<?endif?>
<?endif?>
</table>
