<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

if (SITE_TEMPLATE_ID !== "bitrix24")
{
	return;
}

use Bitrix\Intranet\Binding\Marketplace;
use Bitrix\Intranet\Site\Sections\AutomationSection;
use \Bitrix\Landing\Rights;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

global $APPLICATION;

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/.top.menu_ext.php");

if (!function_exists("getLeftMenuItemLink"))
{
	function getLeftMenuItemLink($sectionId, $defaultLink = "")
	{
		$settings = CUserOptions::GetOption("UI", $sectionId);
		return
			is_array($settings) && isset($settings["firstPageLink"]) && mb_strlen($settings["firstPageLink"]) ?
				$settings["firstPageLink"] :
				$defaultLink;
	}
}

if (!function_exists("getItemLinkId"))
{
	function getItemLinkId($link)
	{
		$menuId = str_replace("/", "_", trim($link, "/"));
		return "top_menu_id_".$menuId;
	}
}

$userId = $GLOBALS["USER"]->GetID();

if (defined("BX_COMP_MANAGED_CACHE"))
{
	global $CACHE_MANAGER;
	$CACHE_MANAGER->registerTag("bitrix24_left_menu");
	$CACHE_MANAGER->registerTag("crm_change_role");
	$CACHE_MANAGER->registerTag("USER_NAME_".$userId);
}

global $USER;

$arMenuB24 = array(
	array(
		GetMessage("TOP_MENU_LIVE_FEED2"),
		file_exists($_SERVER["DOCUMENT_ROOT"].SITE_DIR."stream/") ? SITE_DIR."stream/" : SITE_DIR,
		array(),
		array(
			"name" => "live_feed",
			"counter_id" => "live-feed",
			"menu_item_id" => "menu_live_feed",
		),
		""
	)
);

if ($GLOBALS["USER"]->IsAuthorized() && Loader::includeModule("socialnetwork"))
{
	$arUserActiveFeatures = CSocNetFeatures::GetActiveFeatures(SONET_ENTITY_USER, $GLOBALS["USER"]->GetID());
	$arSocNetFeaturesSettings = CSocNetAllowed::GetAllowedFeatures();

	$allowedFeatures = array();
	foreach (array("tasks", "files", "photo", "blog", "calendar") as $feature)
	{
		$allowedFeatures[$feature] =
			array_key_exists($feature, $arSocNetFeaturesSettings) &&
			array_key_exists("allowed", $arSocNetFeaturesSettings[$feature]) &&
			in_array(SONET_ENTITY_USER, $arSocNetFeaturesSettings[$feature]["allowed"]) &&
			is_array($arUserActiveFeatures) &&
			in_array($feature, $arUserActiveFeatures)
		;
	}

	if ($allowedFeatures["tasks"])
	{
		$arMenuB24[] = [
			GetMessage("TOP_MENU_TASKS"),
			SITE_DIR . "tasks/menu/",
			[],
			[
				"name" => "tasks",
				"counter_id" => "tasks_total",
				"menu_item_id" => "menu_tasks",
				"real_link" => getLeftMenuItemLink(
					"tasks_panel_menu",
					SITE_DIR."company/personal/user/".$userId."/tasks/"
				),
				"sub_link" => SITE_DIR."company/personal/user/".$userId."/tasks/task/edit/0/",
				"top_menu_id" => "tasks_panel_menu",
			],
			"CBXFeatures::IsFeatureEnabled('Tasks')"
		];
	}

	if (
		$allowedFeatures["calendar"]
		&& CBXFeatures::IsFeatureEnabled('Calendar')
		|| CBXFeatures::IsFeatureEnabled('CompanyCalendar')
	)
	{
		$arMenuB24[] = array(
			GetMessage("TOP_MENU_CALENDAR"),
			SITE_DIR."calendar/",
			array(
				SITE_DIR."company/personal/user/".$userId."/calendar/",
				SITE_DIR."calendar/"
			),
			array(
				"real_link" => getLeftMenuItemLink(
					"top_menu_id_calendar",
					$allowedFeatures["calendar"] && CBXFeatures::IsFeatureEnabled('Calendar') ? SITE_DIR."company/personal/user/".$userId."/calendar/" : SITE_DIR."calendar/"
				),
				"menu_item_id" => "menu_calendar",
				"counter_id" => "calendar",
				"top_menu_id" => "top_menu_id_calendar",
				"sub_link" => SITE_DIR."company/personal/user/".$userId."/calendar/?EVENT_ID=NEW",
			),
			""
		);
	}

	if (
		Loader::includeModule("disk")
		&& (
			$allowedFeatures["files"]
			&& CBXFeatures::IsFeatureEnabled('PersonalFiles')
			|| CBXFeatures::IsFeatureEnabled('CommonDocuments')
		)
	)
	{
		$diskEnabled = \Bitrix\Main\Config\Option::get('disk', 'successfully_converted', false);
		$diskPath =
			$diskEnabled === "Y" ?
				SITE_DIR."company/personal/user/".$userId."/disk/path/" :
				SITE_DIR."company/personal/user/".$userId."/files/lib/"
		;

		$arMenuB24[] = array(
			GetMessage("TOP_MENU_DISK"),
			SITE_DIR."docs/",
			array(
				$diskPath,
				SITE_DIR."docs/",
				SITE_DIR."company/personal/user/".$userId."/disk/volume/",
				SITE_DIR."company/personal/user/".$userId."/disk/"
			),
			array(
				"real_link" => getLeftMenuItemLink(
					"top_menu_id_docs",
					CBXFeatures::IsFeatureEnabled('PersonalFiles') ? $diskPath : SITE_DIR."docs/"
				),
				"menu_item_id" => "menu_files",
				"top_menu_id" => "top_menu_id_docs",
			),
			""
		);
		if ($diskEnabled === "Y" && \Bitrix\Main\Config\Option::get('disk', 'documents_enabled', 'N') === 'Y')
		{
			$arMenuB24[] = array(
				GetMessage("TOP_MENU_DISK_DOCUMENTS"),
				SITE_DIR."company/personal/user/".$userId."/disk/documents/",
				[],
				array(
					"menu_item_id" => "menu_documents",
				),
				""
			);
		}
	}
}

if (Loader::includeModule("crm") && CCrmPerms::IsAccessEnabled())
{
	$counterId = CCrmSaleHelper::isWithOrdersMode() ? 'crm_all' : 'crm_all_no_orders';
	$arMenuB24[] = [
		GetMessage("TOP_MENU_CRM"),
		SITE_DIR."crm/menu/",
		[
			SITE_DIR."crm/",
			SITE_DIR."shop/documents/",
			ModuleManager::isModuleInstalled('bitrix24') ? "/contact_center/" : SITE_DIR . "services/contact_center/"
		],
		[
			"real_link" => \Bitrix\Crm\Settings\EntityViewSettings::getDefaultPageUrl(),
			"counter_id" => $counterId,
			"menu_item_id" => "menu_crm_favorite",
			"top_menu_id" => "crm_control_panel_menu"
		],
		""
	];
}
else
{
	$arMenuB24[] = [
		GetMessage("TOP_MENU_CONTACT_CENTER"),
		SITE_DIR . "services/contact_center/",
		[],
		[
			"real_link" => getLeftMenuItemLink(
				"top_menu_id_contact_center",
				SITE_DIR . "services/contact_center/"
			),
			"menu_item_id" => "menu_contact_center",
			"top_menu_id" => "top_menu_id_contact_center",
		],
		"",
	];
}

if (
	\Bitrix\Main\Config\Option::get('intranet', 'left_menu_crm_store_menu', 'Y') === 'Y'
	&& Loader::includeModule('catalog')
)
{
	$arMenuB24[] = [
		GetMessage("MENU_STORE_ACCOUNTING_SECTION"),
		SITE_DIR . 'shop/documents/inventory/',
		[
			SITE_DIR . 'shop/documents/',
		],
		[
			'menu_item_id' => 'menu_crm_store',
		],
		''
	];
}

$landingAvailable = Loader::includeModule('landing') && Rights::hasAdditionalRight(Rights::ADDITIONAL_RIGHTS['menu24']);
if (Loader::includeModule("crm") && CCrmSaleHelper::isShopAccess())
{
	$includeCounter = CCrmSaleHelper::isWithOrdersMode();
	$parameters = [
		'real_link' => getLeftMenuItemLink(
			'store',
			$landingAvailable ? SITE_DIR . 'sites/' : SITE_DIR . 'shop/orders/menu/'
		),
		'menu_item_id' => 'menu_shop',
		'top_menu_id' => 'store',
	];
	if ($includeCounter)
	{
		$parameters['counter_id'] = 'shop_all';
	}

	$arMenuB24[] = [
		GetMessage("TOP_MENU_SITES_AND_STORES"),
		SITE_DIR . "shop/menu/",
		[
			SITE_DIR . "shop/",
			SITE_DIR . "sites/",
		],
		$parameters,
		"",
	];
}
else if ($landingAvailable)
{
	$arMenuB24[] = [
		GetMessage("TOP_MENU_SITES"),
		SITE_DIR . "sites/",
		[],
		[
			"menu_item_id" => "menu_sites",
		],
		""
	];
}

if (Loader::includeModule("sender") && \Bitrix\Sender\Security\Access::current()->canViewAnything())
{
	$arMenuB24[] = [
		GetMessage("TOP_MENU_MARKETING2"),
		SITE_DIR."marketing/",
		[],
		[
			"real_link" => getLeftMenuItemLink(
				"top_menu_id_marketing",
				SITE_DIR."marketing/"
			),
			"menu_item_id" => "menu_marketing",
			'top_menu_id' => 'top_menu_id_marketing',
		],
		""
	];
}

if (CModule::IncludeModule('im'))
{
	$arMenuB24[] = [
		GetMessage('TOP_MENU_IM_MESSENGER'),
		SITE_DIR . 'online/',
		[],
		[
			'counter_id' => 'im-message',
			'menu_item_id' => 'menu_im_messenger',
			'can_be_first_item' => false,
		],
		'CBXFeatures::IsFeatureEnabled("WebMessenger")',
	];
}

if (Loader::includeModule("intranet") && CIntranetUtils::IsExternalMailAvailable())
{
	$warningLink = $mailLink = \Bitrix\Main\Config\Option::get('intranet', 'path_mail_client', SITE_DIR . 'mail/');

	$arMenuB24[] = array(
		GetMessage("TOP_MENU_MAIL"),
		$mailLink,
		array(),
		array(
			"counter_id" => "mail_unseen",
			"warning_link" => $warningLink,
			"warning_title" => GetMessage("MENU_MAIL_CHANGE_SETTINGS"),
			"menu_item_id" => "menu_external_mail",
		),
		""
	);
}

if (Loader::includeModule("socialnetwork"))
{
	$canCreateGroup = \Bitrix\Socialnetwork\Helper\Workgroup::canCreate();

	$groupPath = SITE_DIR."workgroups/";
	$arMenuB24[] = [
		GetMessage("TOP_MENU_GROUPS"),
		$groupPath,
		[],
		[
			"real_link" => getLeftMenuItemLink(
				"sonetgroups_panel_menu",
				$groupPath
			),
			"menu_item_id"=>"menu_all_groups",
			"top_menu_id" => "sonetgroups_panel_menu",
			// todo oh 'counter_id' => 'workgroups',
		] + ($canCreateGroup ? ["sub_link" => SITE_DIR."company/personal/user/".$userId."/groups/create/"] : []),
		"CBXFeatures::IsFeatureEnabled('Workgroups')"
	];
}

$aboutSectionExists = file_exists($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'about/');

$arMenuB24[] = [
	$aboutSectionExists ? Loc::getMessage('TOP_MENU_COMPANY') : Loc::getMessage('TOP_MENU_COMPANY_SECTION'),
	SITE_DIR . 'company/',
	[
		'/timeman/',
		'/kb/',
		'/conference/',
	],
	[
		'real_link' => getLeftMenuItemLink(
			'top_menu_id_company',
			SITE_DIR . 'company/vis_structure.php'
		),
		'menu_item_id' => 'menu_company',
		'top_menu_id' => 'top_menu_id_company',
		'class' => 'menu-company',
	],
];

if ($aboutSectionExists)
{
	$arMenuB24[] = [
		Loc::getMessage('TOP_MENU_ABOUT'),
		SITE_DIR . 'about/',
		[SITE_DIR . 'about/'],
		[
			'real_link' => getLeftMenuItemLink(
				'top_menu_id_about',
				SITE_DIR . 'about/'
			),
			'menu_item_id' => 'menu_about_sect',
			'top_menu_id' => 'top_menu_id_about',
		],
		'',
	];
}

if (Loader::includeModule('intranet') && AutomationSection::isAvailable())
{
	$automationItem = AutomationSection::getRootMenuItem();
	$automationItem[3]['real_link'] = getLeftMenuItemLink(
		"top_menu_id_automation",
		!empty($automationItem[3]['first_item_url']) ? $automationItem[3]['first_item_url'] : $automationItem[1]
	);

	$arMenuB24[] = $automationItem;
}

if ($aboutSectionExists)
{
	$arMenuB24[] = [
		Loc::getMessage('TOP_MENU_ABOUT'),
		SITE_DIR . 'about/',
		[],
		[
			'real_link' => getLeftMenuItemLink(
				'top_menu_id_about',
				SITE_DIR . 'about/'
			),
			'menu_item_id' => 'menu_about_sect',
			'top_menu_id' => 'top_menu_id_about',
		],
		'',
	];
}

//merge with static items from top.menu
foreach ($aMenuLinks as $arItem)
{
	$menuLink = $arItem[1];

	if (preg_match("~/(workgroups|crm|marketplace|docs|timeman|bizproc|company|about|services)/$~i", $menuLink))
	{
		continue;
	}

	$menuId = getItemLinkId($menuLink);
	$arItem[3]["real_link"] = getLeftMenuItemLink($menuId, $menuLink);
	$arItem[3]["top_menu_id"] = $menuId;
	$arMenuB24[] = $arItem;
}

$arMenuB24[] = array(
	GetMessage("TOP_MENU_SERVICES"),
	SITE_DIR."services/",
	array(SITE_DIR."services/"),
	array(
		"real_link" => getLeftMenuItemLink(
			"top_menu_id_services",
			SITE_DIR."services/"
		),
		"menu_item_id"=>"menu_services_sect",
		"top_menu_id" => "top_menu_id_services"
	),
	""
);

$arMenuB24[] = array(
	GetMessage("TOP_MENU_MARKETPLACE_3"),
	SITE_DIR.Marketplace::getBoxMainDirectory(),
	array(SITE_DIR.Marketplace::getBoxMainDirectory()),
	array(
		"real_link" => getLeftMenuItemLink(
			"top_menu_id_marketplace",
			SITE_DIR.Marketplace::getBoxMainDirectory()
		),
		"menu_item_id"=>"menu_marketplace_sect",
		"top_menu_id" => "top_menu_id_marketplace"
	),
	"IsModuleInstalled('rest')"
);

$arMenuB24[] = [
	GetMessage("TOP_MENU_DEVOPS"),
	SITE_DIR . "devops/",
	[SITE_DIR . "devops/"],
	[
		"real_link" => getLeftMenuItemLink(
			"top_menu_id_devops",
			SITE_DIR . "devops/"
		),
		"menu_item_id" => "menu_devops_sect",
		"top_menu_id" => "top_menu_id_devops",
	],
	"IsModuleInstalled('rest')",
];

$arMenuB24[] = Array(
	GetMessage("TOP_MENU_CONFIGS"),
	SITE_DIR."configs/",
	Array(SITE_DIR."configs/"),
	Array(
		"real_link" => getLeftMenuItemLink(
			"top_menu_id_configs",
			SITE_DIR."configs/"
		),
		"menu_item_id" => "menu_configs_sect",
		"top_menu_id" => "top_menu_id_configs"
	),
	'$USER->IsAdmin()'
);

$manager = \Bitrix\Main\DI\ServiceLocator::getInstance()->get('intranet.customSection.manager');
$manager->appendSuperLeftMenuSections($arMenuB24);

$rsSite = CSite::GetList("sort", "asc", $arFilter = array("ACTIVE" => "Y"));
$exSiteId = COption::GetOptionString("extranet", "extranet_site");
while ($site = $rsSite->Fetch())
{
	if ($site["LID"] !== $exSiteId && $site["LID"] !== SITE_ID)
	{
		$url = ((CMain::IsHTTPS()) ? "https://" : "http://").$site["SERVER_NAME"].$site["DIR"];
		$arMenuB24[] = array(
			htmlspecialcharsbx($site["NAME"]),
			htmlspecialcharsbx($url),
			array(),
			array(),
			""
		);
	}
}

$aMenuLinks = $arMenuB24;
