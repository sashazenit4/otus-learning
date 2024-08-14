<?php

$siteId = '';
if(isset($_REQUEST['site_id']) && is_string($_REQUEST['site_id']))
{
	$siteId = mb_substr(preg_replace('/[^a-z0-9_]/i', '', $_REQUEST['site_id']), 0, 2);
}

if($siteId)
{
	define('SITE_ID', $siteId);
}

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

if(\Bitrix\Main\Loader::includeModule('salescenter') && \Bitrix\SalesCenter\Integration\CrmManager::getInstance()->isEnabled())
{
	$APPLICATION->IncludeComponent(
		'bitrix:ui.sidepanel.wrapper',
		'',
		[
			'POPUP_COMPONENT_NAME' => 'bitrix:crm.entity.details.frame',
			'POPUP_COMPONENT_TEMPLATE_NAME' => '',
			'POPUP_COMPONENT_PARAMS' => [
				'ENTITY_TYPE_ID' => CCrmOwnerType::Order,
				'ENTITY_ID' => $request->get('orderId'),
				'ENABLE_TITLE_EDIT' => false,
				'DISABLE_TOP_MENU' => 'Y',
				'EXTRAS' => [
					'IS_SALESCENTER_ORDER_CREATION' => 'Y',
					'SALESCENTER_SESSION_ID' => $request->get('sessionId')
				]
			],
		]
	);
}

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');