<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule('crm'))
	return;

if (WIZARD_INSTALL_DEMO_DATA || COption::GetOptionString("crm", "form_features") == "Y")
{
	$arMenuItem = 	Array(
		GetMessage('CRM_TOP_LINKS_ITEM_NAME'),
		WIZARD_SITE_DIR.'crm/',
		Array(),
		Array(),
		"CBXFeatures::IsFeatureEnabled('crm') && CModule::IncludeModule('crm') && CCrmPerms::IsAccessEnabled()"
	);

	WizardServices::AddMenuItem(WIZARD_SITE_DIR.'.top.menu.php', $arMenuItem, WIZARD_SITE_ID, 7);
}
if (WIZARD_INSTALL_DEMO_DATA || COption::GetOptionString("crm", "form_features") == "Y")
{
	$arUrlRewrite = array();
	if (file_exists(WIZARD_SITE_ROOT_PATH."/urlrewrite.php"))
	{
		include(WIZARD_SITE_ROOT_PATH."/urlrewrite.php");
	}

	CUrlRewriter::Add(array(
		'CONDITION' => '#^'.WIZARD_SITE_DIR.'crm/lead/#',
		'RULE' => '',
		'ID' => 'bitrix:crm.lead',
		'PATH' => WIZARD_SITE_DIR.'crm/lead/index.php'
	));

	CUrlRewriter::Add(array(
		'CONDITION' => '#^'.WIZARD_SITE_DIR.'crm/contact/#',
		'RULE' => '',
		'ID' => 'bitrix:crm.contact',
		'PATH' => WIZARD_SITE_DIR.'crm/contact/index.php'
	));

	CUrlRewriter::Add(array(
		'CONDITION' => '#^'.WIZARD_SITE_DIR.'crm/company/#',
		'RULE' => '',
		'ID' => 'bitrix:crm.company',
		'PATH' => WIZARD_SITE_DIR.'crm/company/index.php'
	));

	CUrlRewriter::Add(array(
		'CONDITION' => '#^'.WIZARD_SITE_DIR.'crm/deal/#',
		'RULE' => '',
		'ID' => 'bitrix:crm.deal',
		'PATH' => WIZARD_SITE_DIR.'crm/deal/index.php'
	));

	CUrlRewriter::Add(array(
		'CONDITION' => '#^'.WIZARD_SITE_DIR.'crm/quote/#',
		'RULE' => '',
		'ID' => 'bitrix:crm.quote',
		'PATH' => WIZARD_SITE_DIR.'crm/quote/index.php'
	));

	CUrlRewriter::Add(array(
		'CONDITION' => '#^'.WIZARD_SITE_DIR.'crm/invoice/#',
		'RULE' => '',
		'ID' => 'bitrix:crm.invoice',
		'PATH' => WIZARD_SITE_DIR.'crm/invoice/index.php'
	));

	CUrlRewriter::Add(array(
		'CONDITION' => '#^'.WIZARD_SITE_DIR.'crm/configs/fields/#',
		'RULE' => '',
		'ID' => 'bitrix:crm.config.fields',
		'PATH' => WIZARD_SITE_DIR.'crm/configs/fields/index.php'
	));

	CUrlRewriter::Add(array(
		'CONDITION' => '#^'.WIZARD_SITE_DIR.'crm/configs/bp/#',
		'RULE' => '',
		'ID' => 'bitrix:crm.config.bp',
		'PATH' => WIZARD_SITE_DIR.'crm/configs/bp/index.php'
	));

	CUrlRewriter::Add(array(
		'CONDITION' => '#^'.WIZARD_SITE_DIR.'crm/configs/perms/#',
		'RULE' => '',
		'ID' => 'bitrix:crm.config.perms',
		'PATH' => WIZARD_SITE_DIR.'crm/configs/perms/index.php'
	));

	CUrlRewriter::Add(array(
		'CONDITION' => '#^'.WIZARD_SITE_DIR.'crm/product/#',
		'RULE' => '',
		'ID' => 'bitrix:crm.product',
		'PATH' => WIZARD_SITE_DIR.'crm/product/index.php'
	));

	CUrlRewriter::Add(array(
		'CONDITION' => '#^'.WIZARD_SITE_DIR.'crm/configs/currency/#',
		'RULE' => '',
		'ID' => 'bitrix:crm.currency',
		'PATH' => WIZARD_SITE_DIR.'crm/configs/currency/index.php'
	));

	CUrlRewriter::Add(array(
		'CONDITION' => '#^'.WIZARD_SITE_DIR.'crm/configs/tax/#',
		'RULE' => '',
		'ID' => 'bitrix:crm.config.tax',
		'PATH' => WIZARD_SITE_DIR.'crm/configs/tax/index.php'
	));

	CUrlRewriter::Add(array(
		'CONDITION' => '#^'.WIZARD_SITE_DIR.'crm/configs/locations/#',
		'RULE' => '',
		'ID' => 'bitrix:crm.config.locations',
		'PATH' => WIZARD_SITE_DIR.'crm/configs/locations/index.php'
	));

	CUrlRewriter::Add(array(
		'CONDITION' => '#^'.WIZARD_SITE_DIR.'crm/configs/ps/#',
		'RULE' => '',
		'ID' => 'bitrix:crm.config.ps',
		'PATH' => WIZARD_SITE_DIR.'crm/configs/ps/index.php'
	));

	CUrlRewriter::Add(array(
		'CONDITION' => '#^'.WIZARD_SITE_DIR.'crm/reports/report/#',
		'RULE' => '',
		'ID' => 'bitrix:crm.report',
		'PATH' => WIZARD_SITE_DIR.'crm/reports/report/index.php'
	));

	CUrlRewriter::Add(array(
		'CONDITION' => '#^'.WIZARD_SITE_DIR.'crm/configs/mailtemplate/#',
		'RULE' => '',
		'ID' => 'bitrix:crm.mail_template',
		'PATH' => WIZARD_SITE_DIR.'crm/configs/mailtemplate/index.php'
	));
	CUrlRewriter::Add(array(
		'CONDITION' => '#^'.WIZARD_SITE_DIR.'crm/configs/exch1c/#',
		'RULE' => '',
		'ID' => 'bitrix:crm.config.exch1c',
		'PATH' => WIZARD_SITE_DIR.'crm/configs/exch1c/index.php'
	));
	CUrlRewriter::Add(array(
		'CONDITION' => '#^'.WIZARD_SITE_DIR.'crm/quote/#',
		'RULE' => '',
		'ID' => 'bitrix:crm.quote',
		'PATH' => WIZARD_SITE_DIR.'crm/quote/index.php'
	));
	CUrlRewriter::Add(array(
		"CONDITION" => '#^'.WIZARD_SITE_DIR.'crm/configs/measure/#',
		"RULE" => '',
		"ID" => 'bitrix:crm.config.measure',
		"PATH" => WIZARD_SITE_DIR.'crm/configs/measure/index.php',
	));
	CUrlRewriter::Add(array(
		"CONDITION" => '#^'.WIZARD_SITE_DIR.'crm/configs/productprops/#',
		"RULE" => '',
		"ID" => "bitrix:crm.config.productprops",
		"PATH" => WIZARD_SITE_DIR.'crm/configs/productprops/index.php',
	));
	CUrlRewriter::Add(array(
		"CONDITION" => "#^/crm/configs/preset/#",
		"RULE" => "",
		"ID" => "bitrix:crm.config.preset",
		"PATH" => "/crm/configs/preset/index.php",
	));
	CUrlRewriter::Add(array(
		'CONDITION' => '#^'.WIZARD_SITE_DIR.'crm/webform/#',
		'RULE' => '',
		'ID' => 'bitrix:crm.webform',
		'PATH' => WIZARD_SITE_DIR.'crm/webform/index.php'
	));
	CUrlRewriter::Add(array(
		"CONDITION" => "#^/crm/configs/mycompany/#",
		"RULE" => "",
		"ID" => "bitrix:crm.company",
		"PATH" => "/crm/configs/mycompany/index.php",
	));
	CUrlRewriter::Add(array(
		"CONDITION" => "#^/crm/configs/deal_category/#",
		"RULE" => "",
		"ID" => "bitrix:crm.deal_category",
		"PATH" => "/crm/configs/deal_category/index.php",
	));
	CUrlRewriter::Add(array(
		"CONDITION" => "#^/crm/activity/#",
		"RULE" => "",
		"ID" => "bitrix:crm.activity",
		"PATH" => "/crm/activity/index.php",
	));
}
if(!WIZARD_IS_RERUN || COption::GetOptionString("crm", "form_features") == "Y")
{
	// desktop on CRM index page
	$arOptions["GADGETS"] = Array (
		"CRM_MY_ACTIVITIES@1494" => Array (
			"COLUMN" => "0",
			"HIDE" => "N",
			"SETTINGS" => Array (
				"TITLE_STD" =>GetMessage('CRM_GADGET_MY_ACTIVITY'),
				"SORT_BY" => "DESC",
				"ITEM_COUNT" => "5"
			)
		),
		"CRM_DEAL_LIST@9562" => Array (
			"COLUMN" => "1",
			"ROW" => "0",
			"HIDE" => "N",
			"SETTINGS" => Array (
				"TITLE_STD" => GetMessage('CRM_GADGET_MY_DEAL_TITLE'),
				"STAGE_ID" => "WON",
				"ONLY_MY" => "N",
				"SORT" => "DATE_MODIFY",
				"SORT_BY" => "DESC",
				"DEAL_COUNT" => "3"
			)
		),
		"CRM_LEAD_LIST@27424" => Array (
			"COLUMN" => "1",
			"ROW" => "2",
			"HIDE" => "N",
			"SETTINGS" => Array (
				"TITLE_STD" =>GetMessage('CRM_GADGET_MY_LEAD_TITLE'),
				"STATUS_ID" => array("NEW","ASSIGNED","DETAILS","CANNOT_CONTACT","IN_PROCESS","ON_HOLD","RESTORED","JUNK"),
				"ONLY_MY" => "N",
				"DATE_CREATE",
				"SORT_BY" => "DESC",
				"LEAD_COUNT" => "3"
			)
		),
		"desktop-actions" => Array (
			"COLUMN" => 2,
			"ROW" => 0,
			"HIDE" => "N"
		)
	);

	WizardServices::SetUserOption('intranet', '~gadgets_crm', $arOptions, $common = true);
}
if (WIZARD_INSTALL_DEMO_DATA && WIZARD_SITE_ID == "s1")
{
	$CCrmRole = new CCrmRole();
	$arRoles = array(
		'adm' => array(
			'NAME' => GetMessage('CRM_ROLE_ADMIN'),
			'RELATION' => array(
				'LEAD' => array(
					'READ' => array('-' => 'X'),
					'EXPORT' => array('-' => 'X'),
					'IMPORT' => array('-' => 'X'),
					'ADD' => array('-' => 'X'),
					'WRITE' => array('-' => 'X'),
					'DELETE' => array('-' => 'X')
				),
				'DEAL' => array(
					'READ' => array('-' => 'X'),
					'EXPORT' => array('-' => 'X'),
					'IMPORT' => array('-' => 'X'),
					'ADD' => array('-' => 'X'),
					'WRITE' => array('-' => 'X'),
					'DELETE' => array('-' => 'X')
				),
				'CONTACT' => array(
					'READ' => array('-' => 'X'),
					'EXPORT' => array('-' => 'X'),
					'IMPORT' => array('-' => 'X'),
					'ADD' => array('-' => 'X'),
					'WRITE' => array('-' => 'X'),
					'DELETE' => array('-' => 'X')
				),
				'COMPANY' => array(
					'READ' => array('-' => 'X'),
					'EXPORT' => array('-' => 'X'),
					'IMPORT' => array('-' => 'X'),
					'ADD' => array('-' => 'X'),
					'WRITE' => array('-' => 'X'),
					'DELETE' => array('-' => 'X')
				),
				'QUOTE' => array(
					'READ' => array('-' => 'X'),
					'EXPORT' => array('-' => 'X'),
					'IMPORT' => array('-' => 'X'),
					'ADD' => array('-' => 'X'),
					'WRITE' => array('-' => 'X'),
					'DELETE' => array('-' => 'X')
				),
				'INVOICE' => array(
					'READ' => array('-' => 'X'),
					'EXPORT' => array('-' => 'X'),
					'IMPORT' => array('-' => 'X'),
					'ADD' => array('-' => 'X'),
					'WRITE' => array('-' => 'X'),
					'DELETE' => array('-' => 'X')
				),
				'WEBFORM' => array(
					'READ' => array('-' => 'X'),
					'WRITE' => array('-' => 'X')
				),
				'CONFIG' => array(
					'WRITE' => array('-' => 'X')
				)
			)
		),
		'dir' => array(
			'NAME' => GetMessage('CRM_ROLE_DIRECTOR'),
			'RELATION' => array(
				'LEAD' => array(
					'READ' => array('-' => 'X'),
					'EXPORT' => array('-' => 'X'),
					'IMPORT' => array('-' => 'X'),
					'ADD' => array('-' => 'X'),
					'WRITE' => array('-' => 'X'),
					'DELETE' => array('-' => 'X')
				),
				'DEAL' => array(
					'READ' => array('-' => 'X'),
					'EXPORT' => array('-' => 'X'),
					'IMPORT' => array('-' => 'X'),
					'ADD' => array('-' => 'X'),
					'WRITE' => array('-' => 'X'),
					'DELETE' => array('-' => 'X')
				),
				'CONTACT' => array(
					'READ' => array('-' => 'X'),
					'EXPORT' => array('-' => 'X'),
					'IMPORT' => array('-' => 'X'),
					'ADD' => array('-' => 'X'),
					'WRITE' => array('-' => 'X'),
					'DELETE' => array('-' => 'X')
				),
				'COMPANY' => array(
					'READ' => array('-' => 'X'),
					'EXPORT' => array('-' => 'X'),
					'IMPORT' => array('-' => 'X'),
					'ADD' => array('-' => 'X'),
					'WRITE' => array('-' => 'X'),
					'DELETE' => array('-' => 'X')
				),
				'QUOTE' => array(
					'READ' => array('-' => 'X'),
					'EXPORT' => array('-' => 'X'),
					'IMPORT' => array('-' => 'X'),
					'ADD' => array('-' => 'X'),
					'WRITE' => array('-' => 'X'),
					'DELETE' => array('-' => 'X')
				),
				'INVOICE' => array(
					'READ' => array('-' => 'X'),
					'EXPORT' => array('-' => 'X'),
					'IMPORT' => array('-' => 'X'),
					'ADD' => array('-' => 'X'),
					'WRITE' => array('-' => 'X'),
					'DELETE' => array('-' => 'X')
				),
				'WEBFORM' => array(
					'READ' => array('-' => 'X'),
					'WRITE' => array('-' => 'X')
				)
			)
		),
		'chif' => array(
			'NAME' => GetMessage('CRM_ROLE_CHIF'),
			'RELATION' => array(
				'LEAD' => array(
					'READ' => array('-' => 'D'),
					'EXPORT' => array('-' => 'D'),
					'IMPORT' => array('-' => 'D'),
					'ADD' => array('-' => 'D'),
					'WRITE' => array('-' => 'D'),
					'DELETE' => array('-' => 'D')
				),
				'DEAL' => array(
					'READ' => array('-' => 'D'),
					'EXPORT' => array('-' => 'D'),
					'IMPORT' => array('-' => 'D'),
					'ADD' => array('-' => 'D'),
					'WRITE' => array('-' => 'D'),
					'DELETE' => array('-' => 'D')
				),
				'CONTACT' => array(
					'READ' => array('-' => 'D'),
					'EXPORT' => array('-' => 'D'),
					'IMPORT' => array('-' => 'D'),
					'ADD' => array('-' => 'D'),
					'WRITE' => array('-' => 'D'),
					'DELETE' => array('-' => 'D')
				),
				'COMPANY' => array(
					'READ' => array('-' => 'X'),
					'EXPORT' => array('-' => 'X'),
					'IMPORT' => array('-' => 'X'),
					'ADD' => array('-' => 'X'),
					'WRITE' => array('-' => 'X'),
					'DELETE' => array('-' => 'X')
				),
				'QUOTE' => array(
					'READ' => array('-' => 'D'),
					'EXPORT' => array('-' => 'D'),
					'IMPORT' => array('-' => 'D'),
					'ADD' => array('-' => 'D'),
					'WRITE' => array('-' => 'D'),
					'DELETE' => array('-' => 'D')
				),
				'INVOICE' => array(
					'READ' => array('-' => 'D'),
					'EXPORT' => array('-' => 'D'),
					'IMPORT' => array('-' => 'D'),
					'ADD' => array('-' => 'D'),
					'WRITE' => array('-' => 'D'),
					'DELETE' => array('-' => 'D')
				),
				'WEBFORM' => array(
					'READ' => array('-' => 'D'),
					'WRITE' => array('-' => 'D')
				)
			)
		),
		'man' => array(
			'NAME' => GetMessage('CRM_ROLE_MAN'),
			'RELATION' => array(
				'LEAD' => array(
					'READ' => array('-' => 'A'),
					'EXPORT' => array('-' => 'A'),
					'IMPORT' => array('-' => 'A'),
					'ADD' => array('-' => 'A'),
					'WRITE' => array('-' => 'A'),
					'DELETE' => array('-' => 'A')
				),
				'DEAL' => array(
					'READ' => array('-' => 'A'),
					'EXPORT' => array('-' => 'A'),
					'IMPORT' => array('-' => 'A'),
					'ADD' => array('-' => 'A'),
					'WRITE' => array('-' => 'A'),
					'DELETE' => array('-' => 'A')
				),
				'CONTACT' => array(
					'READ' => array('-' => 'A'),
					'EXPORT' => array('-' => 'A'),
					'IMPORT' => array('-' => 'A'),
					'ADD' => array('-' => 'A'),
					'WRITE' => array('-' => 'A'),
					'DELETE' => array('-' => 'A')
				),
				'COMPANY' => array(
					'READ' => array('-' => 'X'),
					'EXPORT' => array('-' => 'X'),
					'IMPORT' => array('-' => 'X'),
					'ADD' => array('-' => 'X'),
					'WRITE' => array('-' => 'X'),
					'DELETE' => array('-' => 'X')
				),
				'QUOTE' => array(
					'READ' => array('-' => 'A'),
					'EXPORT' => array('-' => 'A'),
					'IMPORT' => array('-' => 'A'),
					'ADD' => array('-' => 'A'),
					'WRITE' => array('-' => 'A'),
					'DELETE' => array('-' => 'A')
				),
				'INVOICE' => array(
					'READ' => array('-' => 'A'),
					'EXPORT' => array('-' => 'A'),
					'IMPORT' => array('-' => 'A'),
					'ADD' => array('-' => 'A'),
					'WRITE' => array('-' => 'A'),
					'DELETE' => array('-' => 'A')
				),
				'WEBFORM' => array(
					'READ' => array('-' => 'A'),
					'WRITE' => array('-' => 'A')
				)
			)
		)
	);

	$iRoleIDAdm = $iRoleIDDir = $iRoleIDChif = $iRoleIDMan = 0;
	$obRole = CCrmRole::GetList(array(), array());
	while ($arRole = $obRole->Fetch())
	{
		if ($arRole['NAME'] == GetMessage('CRM_ROLE_ADMIN'))
			$iRoleIDAdm = $arRole['ID'];
		else if ($arRole['NAME'] == GetMessage('CRM_ROLE_DIRECTOR'))
			$iRoleIDDir = $arRole['ID'];
		else if ($arRole['NAME'] == GetMessage('CRM_ROLE_CHIF'))
			$iRoleIDChif = $arRole['ID'];
		else if ($arRole['NAME'] == GetMessage('CRM_ROLE_MAN'))
			$iRoleIDMan = $arRole['ID'];
	}

	$arRel = array();
	if ($iRoleIDAdm <= 0)
		$iRoleIDAdm = $CCrmRole->Add($arRoles['adm']);
	if ($iRoleIDDir <= 0)
		$iRoleIDDir = $CCrmRole->Add($arRoles['dir']);
	if (WIZARD_DIRECTION_GROUP > 0)
		$arRel['G'.WIZARD_DIRECTION_GROUP] = array($iRoleIDDir);
	if ($iRoleIDChif <= 0)
		$iRoleIDChif = $CCrmRole->Add($arRoles['chif']);
	if ($iRoleIDMan <= 0)
		$iRoleIDMan = $CCrmRole->Add($arRoles['man']);
	if (WIZARD_MARKETING_AND_SALES_GROUP > 0)
		$arRel['G'.WIZARD_MARKETING_AND_SALES_GROUP] = array($iRoleID);
	$CCrmRole->SetRelation($arRel);


	/* INSTALL DEMO-DATA */
	// copy files
	CopyDirFiles(WIZARD_ABSOLUTE_PATH."/site/services/crm/images/", WIZARD_SITE_PATH.'/upload/crm', true, true);

	// Create default product catalog
	$catalogID =  CCrmCatalog::EnsureDefaultExists();
	$currencyID = CCrmCurrency::GetBaseCurrencyID();

	// Creation of demo products
	require_once("product.demo.php");
	if (COption::GetOptionString('crm', '~CRM_INVOICE_INSTALL_12_5_7', 'N') !== 'Y')
	{
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/crm/install/sale_link.php");
	}
	CCrmProductDemo::Create($catalogID);

	// Add lead
	require_once("lead.demo.php");
	$CCrmLead = new CCrmLead();
	foreach($arLeads as $ID => $arParams)
	{
		$arProductRows = null;
		if(isset($arParams['PRODUCT_ROWS']))
		{
			$arProductRows = $arParams['PRODUCT_ROWS'];
			unset($arParams['PRODUCT_ROWS']);
		}

		$arParams['CURRENCY_ID'] = $currencyID;
		$leadID = $CCrmLead->Add($arParams);
		$arLeads[$ID]['ID'] = $leadID;

		if(is_array($arProductRows))
		{
			foreach($arProductRows as &$arProductRow)
			{
				$originID = $arProductRow['ORIGIN_ID'];
				$arProduct =  CCrmProduct::GetByOriginID($originID, $catalogID);
				if(!is_array($arProduct))
				{
					continue;
				}

				CCrmLead::SaveProductRows(
					$leadID,
					array(
						array(
							'PRODUCT_ID' => intval($arProduct['ID']),
							'PRICE' => doubleval($arProduct['PRICE']),
							'QUANTITY' => 1
						)
					)
				);
			}
		}
	}

	// Add Contact
	require_once("contact.demo.php");
	$CCrmContact = new CCrmContact();
	foreach($arContacts as $ID => $arParams)
	{
		$arContacts[$ID]['ID'] = $CCrmContact->Add($arParams);
	}

	// Add Company
	require_once("company.demo.php");
	$CCrmCompany = new CCrmCompany();
	foreach($arCompany as $ID => $arParams)
	{
		$arCompany[$ID]['ID'] = $CCrmCompany->Add($arParams);
	}

	// Add Deal
	require_once("deal.demo.php");
	$CCrmDeal = new CCrmDeal();
	foreach($arDeals as $ID => &$arParams)
	{
		$arProductRows = null;
		if(isset($arParams['PRODUCT_ROWS']))
		{
			$arProductRows = $arParams['PRODUCT_ROWS'];
			unset($arParams['PRODUCT_ROWS']);
		}

		$arParams['CURRENCY_ID'] = $currencyID;
		$dealID = $CCrmDeal->Add($arParams);
		$arDeals[$ID]['ID'] = $dealID;

		if(is_array($arProductRows))
		{
			foreach($arProductRows as &$arProductRow)
			{
				$originID = $arProductRow['ORIGIN_ID'];
				$arProduct =  CCrmProduct::GetByOriginID($originID, $catalogID);
				if(!is_array($arProduct))
				{
					continue;
				}

				CCrmDeal::SaveProductRows(
					$dealID,
					array(
						array(
							'PRODUCT_ID' => intval($arProduct['ID']),
							'PRICE' => doubleval($arProduct['PRICE']),
							'QUANTITY' => 1
						)
					)
				);
			}
		}
	}

	// Add event
	require_once("event.demo.php");
	$CCrmEvent = new CCrmEvent();
	foreach($arEvents as $ID => $arParams)
	{
		$arEvents[$ID]['ID'] = $CCrmEvent->Add($arParams);
	}

	// Add relation
	$arParams = Array('COMPANY_ID' => $arCompany['39']['ID'], 'CONTACT_ID' => $arContacts['51']['ID']);
	$CCrmLead->Update($arLeads['57']['ID'], $arParams);

	// Add activity
	require_once("activity.demo.php");
	$CCrmActivity = new CCrmActivity();
	foreach($arActivities as $ID => $arParams)
	{
		$activityId = $CCrmActivity->Add($arParams["FIELDS"], false, false);
		CCrmActivity::SaveCommunications($activityId, array($arParams["COMMUNICATIONS"]), $arParams["FIELDS"], false, false);
	}
	
	//Add invoice
	CCrmInvoice::installDisableSaleEvents();
	require_once("invoice.demo.php");
	$CCrmInvoice = new CCrmInvoice;
	$b_false = false;
	foreach($arInvoices as $arInvoice)
	{
		$CCrmInvoice->Add($arInvoice, $b_false, WIZARD_SITE_ID);
	}
}

COption::SetOptionString("crm", "form_features", "N");
?>