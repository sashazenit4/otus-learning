<?
define("STOP_STATISTICS", true);
use Bitrix\Main\Loader;
use Bitrix\Main\UserTable;
use Bitrix\Main\DI\ServiceLocator;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$MODE = $_GET['MODE'] ? $_GET['MODE'] : 'GET';

if ($MODE == 'VIEW')
{
	$APPLICATION->includeComponent(
		'bitrix:intranet.absence.calendar.view',
		in_array($_GET['VIEW'], array('day', 'week', 'month')) ? $_GET['VIEW'] : '',
		array()
	);
}
elseif ($MODE == 'INFO')
{
	if ($_GET['ID'])
	{
		$APPLICATION->IncludeComponent(
			'otus:vacation.schedule',
			'', 
			array(
				"AJAX_CALL" => "INFO",
				'SITE_ID' => $_GET['SITE_ID'],
				'ID' => intval($_GET['ID']),
				'IBLOCK_ID' => intval($_GET['IBLOCK']),
				'TYPE' => intval($_GET['TYPE']),
			)
		);
	}
}
elseif ($MODE == 'GET')
{
	if (
		\Bitrix\Main\Loader::includeModule('bitrix24')
		&& COption::GetOptionString('bitrix24', 'absence_limits_enabled', '') === 'Y'
		&& !\Bitrix\Bitrix24\Feature::isFeatureEnabled('absence')
	)
	{
		return;
	}

	Loader::includeModule('iblock');

	$user = UserTable::getList([
		'select' => ['ID', 'UF_DEPARTMENT'],
		'filter' => [
			'=ID' => $USER->GetID(),
		],
	])->fetch();

	$departmentId = '';

	if ($user) {
		$departmentId = $user['UF_DEPARTMENT'][0];
		$isChief = false;
		$departments = \CIBlockSection::getList([],
			[
				'=ID' => $departmentId,
                                'IBLOCK_ID' => Bitrix\Main\Config\Option::get('intranet', 'iblock_structure', 5),
			],
			false,
			['ID', 'UF_HEAD'],
		)->fetch();
		if ($departments['UF_HEAD'] == $user['ID']) {
			$isChief = true;
		} else {
			$isChief = false;
		}
	}

	if (!$isChief && !$USER->isAdmin()) {
		$usersToShow = [$USER->getId()];
	}

	if (!empty($_REQUEST['DEPARTMENT'])) {
		$deps = $_REQUEST['DEPARTMENT'];
	} elseif($USER->isAdmin()) {
		$deps = null;
	} else {
		$deps = $departmentId;
	}

	$APPLICATION->IncludeComponent(
		'otus:vacation.schedule',
		'',
		array(
			"AJAX_CALL" => "DATA", 
			"CALLBACK" => 'jsBXAC.SetData',
			'SITE_ID' => $_REQUEST['site_id'],
			'IBLOCK_ID' => $_REQUEST['iblock_id'],
			'CALENDAR_IBLOCK_ID' => $_REQUEST['calendar_iblock_id'],
			'USER_FILTER' => !empty($usersToShow) ? $usersToShow : null,
			"FILTER_SECTION_CURONLY" => $_REQUEST['section_flag'] == 'Y' ? 'Y' : 'N',
			"TS_START" => $_REQUEST['TS_START'],
			"TS_FINISH" => $_REQUEST['TS_FINISH'],
			'PAGE_NUMBER' => $_REQUEST['PAGE_NUMBER'],
			"TYPES" => $_REQUEST['TYPES'],
			"DEPARTMENT" => $deps,
			"SHORT_EVENTS" => $_REQUEST['SHORT_EVENTS'],
			"USERS_ALL" => $_REQUEST['USERS_ALL'],
			"CURRENT_DATA_ID" => $_REQUEST['current_data_id']
		)
	); 

}
//require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
?>
