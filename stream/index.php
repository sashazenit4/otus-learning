<?php

/**
 * @global  \CMain $APPLICATION
 * @global  \CUser $USER
 */

use Bitrix\Intranet\Integration\Wizards\Portal\Ids;

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

$APPLICATION->SetPageProperty('NOT_SHOW_NAV_CHAIN', 'Y');
$APPLICATION->SetPageProperty('title', htmlspecialcharsbx(COption::GetOptionString('main', 'site_name', 'Bitrix24')));
\CModule::IncludeModule('intranet');

if (SITE_TEMPLATE_ID !== 'bitrix24')
{
	return;
}

$APPLICATION->IncludeComponent(
	'bitrix:socialnetwork.log.ex',
	'',
	[
		'PATH_TO_SEARCH_TAG' => SITE_DIR.'search/?tags=#tag#',
		'SET_NAV_CHAIN' => 'Y',
		'SET_TITLE' => 'Y',
		'ITEMS_COUNT' => '32',
		'NAME_TEMPLATE' => CSite::GetNameFormat(),
		'SHOW_LOGIN' => 'Y',
		'SHOW_YEAR' => 'M',
		'CACHE_TYPE' => 'A',
		'CACHE_TIME' => '3600',
		'PATH_TO_CONPANY_DEPARTMENT' => SITE_DIR.'company/structure.php?set_filter_structure=Y&structure_UF_DEPARTMENT=#ID#',
		'SHOW_EVENT_ID_FILTER' => 'Y',
		'SHOW_SETTINGS_LINK' => 'Y',
		'SET_LOG_CACHE' => 'Y',
		'USE_COMMENTS' => 'Y',
		'BLOG_ALLOW_POST_CODE' => 'Y',
		'BLOG_GROUP_ID' => Ids::getBlogId(),
		'PHOTO_USER_IBLOCK_TYPE' => 'photos',
		'PHOTO_USER_IBLOCK_ID' => Ids::getIblockId('user_photogallery'),
		'PHOTO_USE_COMMENTS' => 'Y',
		'PHOTO_COMMENTS_TYPE' => 'FORUM',
		'PHOTO_FORUM_ID' => Ids::getForumId('PHOTOGALLERY_COMMENTS'),
		'PHOTO_USE_CAPTCHA' => 'N',
		'FORUM_ID' => Ids::getForumId('USERS_AND_GROUPS'),
		'PAGER_DESC_NUMBERING' => 'N',
		'AJAX_MODE' => 'N',
		'AJAX_OPTION_SHADOW' => 'N',
		'AJAX_OPTION_HISTORY' => 'N',
		'AJAX_OPTION_JUMP' => 'N',
		'AJAX_OPTION_STYLE' => 'Y',
		'CONTAINER_ID' => 'log_external_container',
		'SHOW_RATING' => '',
		'RATING_TYPE' => '',
		'NEW_TEMPLATE' => 'Y',
		'AVATAR_SIZE' => 100,
		'AVATAR_SIZE_COMMENT' => 100,
		'AUTH' => 'Y',
	]
);
?>

<?php
$APPLICATION->IncludeComponent(
	'bitrix:intranet.bitrix24.banner',
	'',
	[],
	null,
	['HIDE_ICONS' => 'N']
);?>

<?php
if (CModule::IncludeModule('pull'))
{
	$APPLICATION->IncludeComponent('bitrix:intranet.ustat.online', '', [], false);
}

if (CModule::IncludeModule('intranet'))
{
	$APPLICATION->IncludeComponent('bitrix:intranet.ustat.status', '', array(),	false);
}

if (CModule::IncludeModule('calendar'))
{
	$APPLICATION->IncludeComponent(
		'bitrix:calendar.events.list',
		'widget',
		[
			'CALENDAR_TYPE' => 'user',
			'B_CUR_USER_LIST' => 'Y',
			'INIT_DATE' => '',
			'FUTURE_MONTH_COUNT' => '1',
			'DETAIL_URL' => SITE_DIR.'company/personal/user/#user_id#/calendar/',
			'EVENTS_COUNT' => '5',
			'CACHE_TYPE' => 'N',
			'CACHE_TIME' => '3600'
		],
		false
	);
}

if (CModule::IncludeModule('tasks'))
{
	$APPLICATION->IncludeComponent(
		'bitrix:tasks.widget.rolesfilter',
		'',
		[
			'USER_ID' => $USER->GetID(),
			'PATH_TO_TASKS' => SITE_DIR.'company/personal/user/'.$USER->GetID().'/tasks/',
			'PATH_TO_TASKS_CREATE' => SITE_DIR.'company/personal/user/'.$USER->GetID().'/tasks/task/edit/0/',
		],
		null,
		['HIDE_ICONS' => 'N']
	);
}

if ($USER->IsAuthorized())
{
	$APPLICATION->IncludeComponent(
		'bitrix:socialnetwork.blog.blog',
		'important',
		[
			'BLOG_URL' => '',
			'FILTER' => ['=UF_BLOG_POST_IMPRTNT' => 1, '!POST_PARAM_BLOG_POST_IMPRTNT' => ['USER_ID' => $USER->GetId(), 'VALUE' => 'Y']],
			'FILTER_NAME' => '',
			'YEAR' => '',
			'MONTH' => '',
			'DAY' => '',
			'CATEGORY_ID' => '',
			'GROUP_ID' => [],
			'USER_ID' => $USER->GetId(),
			'SOCNET_GROUP_ID' => 0,
			'SORT' => [],
			'SORT_BY1' => '',
			'SORT_ORDER1' => '',
			'SORT_BY2' => '',
			'SORT_ORDER2' => '',
			//************** Page settings **************************************
			'MESSAGE_COUNT' => 0,
			'NAV_TEMPLATE' => '',
			'PAGE_SETTINGS' => ['bDescPageNumbering' => false, 'nPageSize' => 10],
			//************** URL ************************************************
			'BLOG_VAR' => '',
			'POST_VAR' => '',
			'USER_VAR' => '',
			'PAGE_VAR' => '',
			'PATH_TO_BLOG' => SITE_DIR.'company/personal/user/#user_id#/blog/',
			'PATH_TO_BLOG_CATEGORY' => '',
			'PATH_TO_BLOG_POSTS' => SITE_DIR.'company/personal/user/#user_id#/blog/important/',
			'PATH_TO_POST' => SITE_DIR.'company/personal/user/#user_id#/blog/#post_id#/',
			'PATH_TO_POST_EDIT' => SITE_DIR.'company/personal/user/#user_id#/blog/edit/#post_id#/',
			'PATH_TO_USER' => SITE_DIR.'company/personal/user/#user_id#/',
			'PATH_TO_SMILE' => '/bitrix/images/socialnetwork/smile/',
			//************** ADDITIONAL *****************************************
			'DATE_TIME_FORMAT' => (LANGUAGE_ID == 'en') ? 'F j, Y h:i a' : ((LANGUAGE_ID == 'de') ? 'j. F Y H:i:s' : 'd.m.Y H:i:s'),
			'NAME_TEMPLATE' => '',
			'SHOW_LOGIN' => 'Y',
			'AVATAR_SIZE' => 100,
			'SET_TITLE' => 'N',
			'SHOW_RATING' => 'N',
			'RATING_TYPE' => '',
			'MESSAGE_LENGTH' => 56,
			//************** CACHE **********************************************
			'CACHE_TYPE' => 'A',
			'CACHE_TIME' => 3600,
			'CACHE_TAGS' => ['IMPORTANT', 'IMPORTANT'.$USER->GetId()],
			//************** Template Settings **********************************
			'OPTIONS' => [['name' => 'BLOG_POST_IMPRTNT', 'value' => 'Y']],
		],
		null
	);
}

$APPLICATION->IncludeComponent(
	'bitrix:blog.popular_posts',
	'widget',
	[
		'GROUP_ID' => 1,
		'SORT_BY1' => 'RATING_TOTAL_VALUE',
		'MESSAGE_COUNT' => '5',
		'PERIOD_DAYS' => '8',
		'MESSAGE_LENGTH' => '100',
		'DATE_TIME_FORMAT' => (LANGUAGE_ID == 'en') ? 'F j, Y h:i a' : ((LANGUAGE_ID == 'de') ? 'j. F Y H:i:s' : 'd.m.Y H:i:s'),
		'PATH_TO_BLOG' => SITE_DIR.'company/personal/user/#user_id#/blog/',
		'PATH_TO_GROUP_BLOG_POST' => SITE_DIR.'workgroups/group/#group_id#/blog/#post_id#/',
		'PATH_TO_POST' => SITE_DIR.'company/personal/user/#user_id#/blog/#post_id#/',
		'PATH_TO_USER' => SITE_DIR.'company/personal/user/#user_id#/',
		'CACHE_TYPE' => 'A',
		'CACHE_TIME' => '3600',
		'SEO_USER' => 'Y',
		'USE_SOCNET' => 'Y',
		'WIDGET_MODE' => 'Y',
		],
		false
	);
?>

<?php
$APPLICATION->IncludeComponent(
	'bitrix:intranet.structure.birthday.nearest',
	'widget',
	[
		'NUM_USERS' => '4',
		'NAME_TEMPLATE' => '',
		'SHOW_LOGIN' => 'Y',
		'CACHE_TYPE' => 'A',
		'CACHE_TIME' => '86450',
		'CACHE_DATE' => date('dmy'),
		'SHOW_YEAR' => 'N',
		'DETAIL_URL' => SITE_DIR.'company/personal/user/#USER_ID#/',
		'DEPARTMENT' => '0',
		'AJAX_OPTION_ADDITIONAL' => ''
	]
);?>

<?php
if(CModule::IncludeModule('bizproc'))
{
	$APPLICATION->IncludeComponent(
		'bitrix:bizproc.task.list',
		'widget',
		[
			'COUNTERS_ONLY' => 'Y',
			'USER_ID' => $USER->GetID(),
			'PATH_TO_BP_TASKS' => SITE_DIR.'company/personal/bizproc/',
			'PATH_TO_MY_PROCESSES' => SITE_DIR.'company/personal/processes/',
		],
		null,
		['HIDE_ICONS' => 'N']
	);
}

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
