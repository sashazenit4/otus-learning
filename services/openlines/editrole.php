<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/intranet/public/services/openlines/editrole.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_after.php');

$APPLICATION->SetTitle(GetMessage('OL_PAGE_EDIT_ROLE_TITLE'));
?>
<?
if($_GET['IFRAME'] !== 'Y')
{
	$APPLICATION->IncludeComponent(
		'bitrix:imopenlines.menu.top',
		'',
		[],
		false
	);
}
?>
<?$APPLICATION->IncludeComponent('bitrix:ui.sidepanel.wrapper',
								 '',
								 array(
									 'POPUP_COMPONENT_NAME' => 'bitrix:imopenlines.settings.perms.role.edit',
									 'POPUP_COMPONENT_TEMPLATE_NAME' => ''
								 )
);?>

<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>
