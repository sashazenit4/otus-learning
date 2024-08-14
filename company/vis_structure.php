<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/intranet/public/company/vis_structure.php');
$APPLICATION->SetTitle(GetMessage('COMPANY_TITLE'));
$APPLICATION->AddChainItem(GetMessage('COMPANY_TITLE'), 'vis_structure.php');
?><?php
$APPLICATION->IncludeComponent(
	'bitrix:intranet.structure.visual',
	'.default',
	[
		'DETAIL_URL' => SITE_DIR . 'company/structure.php?set_filter_structure=Y&structure_UF_DEPARTMENT=#ID#',
		'PROFILE_URL' => SITE_DIR . 'company/personal/user/#ID#/',
		'PM_URL' => SITE_DIR . 'company/personal/messages/chat/#ID#/',
		'NAME_TEMPLATE' => '',
		'USE_USER_LINK' => 'Y',
	],
	false
);
?><?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');