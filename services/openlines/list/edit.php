<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/intranet/public/services/openlines/list/edit.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_after.php');

$APPLICATION->SetTitle(GetMessage('OL_PAGE_LINES_EDIT_TITLE'));
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
								 [
									 'POPUP_COMPONENT_NAME' => 'bitrix:imopenlines.lines.edit',
									 'POPUP_COMPONENT_TEMPLATE_NAME' => '',
									 'USE_PADDING' => false
								 ]
);?>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>
