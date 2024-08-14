<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

$APPLICATION->IncludeComponent(
	'bitrix:crm.order.matcher',
	'.default',
	[
		'SEF_MODE' => 'Y',
		'SEF_FOLDER' => '/shop/orderform/',
		'SEF_URL_TEMPLATES' => [
			'form' => '#person_type_id#/',
			'property' => '#person_type_id#/prop/#property_id#/'
		],
		'VARIABLE_ALIASES' => [
			'form' => [],
			'property' => [],
		]
	]
);

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');