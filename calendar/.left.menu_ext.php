<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/calendar/.left.menu_ext.php");

$userId = $USER->getId();

$aMenuLinks = [];

if (
	CBXFeatures::IsFeatureEnabled('Calendar')
	&& CModule::IncludeModule('socialnetwork')
)
{
	$itemConfig = [
		'menu_item_id' => 'menu_my_calendar',
		'counter_id' => 'calendar',
	];

	if (class_exists('\Bitrix\Calendar\Internals\Counter'))
	{
		$myCalendarConstantName = '\Bitrix\Calendar\Internals\Counter\CounterDictionary::COUNTER_MY';
		$myCalendarCounterName = defined($myCalendarConstantName)
			? constant($myCalendarConstantName)
			: null;
		if ($myCalendarCounterName)
		{
			$myCalendarCounter = \Bitrix\Calendar\Internals\Counter::getInstance((int)$userId)
				->get($myCalendarCounterName);
			$itemConfig['counter_id'] = 'calendar_my';
			$itemConfig['counter_num'] = $myCalendarCounter;
		}
	}

	$aMenuLinks[] = array(
		GetMessage("MENU_CALENDAR_USER"),
		SITE_DIR."company/personal/user/".$userId."/calendar/",
		array(),
		$itemConfig,
		""
	);

	$aMenuLinks[] = array(
		GetMessage("MENU_CALENDAR_COMPANY"),
		SITE_DIR."calendar/",
		array(),
		array(
			"menu_item_id" => "menu_company_calendar"
		),
		"CBXFeatures::IsFeatureEnabled('CompanyCalendar')"
	);

	$aMenuLinks[] = array(
		GetMessage("MENU_CALENDAR_ROOMS"),
		SITE_DIR."calendar/rooms/",
		array(),
		array(
			"menu_item_id" => "menu_rooms"
		),
		""
	);

	if (
		class_exists('\Bitrix\Calendar\OpenEvents\Feature')
		&& \Bitrix\Calendar\OpenEvents\Feature::getInstance()->isAvailable()
	)
	{
		$openEventsConstantName = '\Bitrix\Calendar\Internals\Counter\CounterDictionary::COUNTER_OPEN_EVENTS';
		$openEventsCounterName = defined($openEventsConstantName)
			? constant($openEventsConstantName)
			: 'calendar_open_events';
		$openEventsCounter = \Bitrix\Calendar\Internals\Counter::getInstance((int)$userId)
			->get($openEventsCounterName);

		$aMenuLinks[] = [
			GetMessage('MENU_CALENDAR_OPEN'),
			'/calendar/open/',
			array(),
			array(
				'menu_item_id' => 'open',
				'counter_id' => 'calendar_open_events',
				'counter_num' => $openEventsCounter,
			),
			'',
		];
	}
}
