<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/intranet/public/services/contact_center/lines_edit/index.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_after.php');
$APPLICATION->SetTitle(GetMessage('OL_PAGE_LINES_EDIT_TITLE'));

$APPLICATION->IncludeComponent('bitrix:ui.sidepanel.wrapper',
	'',
	[
		'POPUP_COMPONENT_NAME' => 'bitrix:imopenlines.lines.edit',
		'POPUP_COMPONENT_TEMPLATE_NAME' => '',
		'USE_PADDING' => false,
		'CLOSE_AFTER_SAVE' => true
	]
);

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
