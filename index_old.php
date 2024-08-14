<?php
/**
 * @global  \CMain $APPLICATION
 */
use \Bitrix\Intranet;
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/intranet/public/index.php');
$APPLICATION->SetTitle(GetMessage('CP_WELCOME'));
$APPLICATION->SetPageProperty('NOT_SHOW_NAV_CHAIN', 'Y');
\CModule::IncludeModule('intranet');

?><?php
	$APPLICATION->IncludeComponent(
	'bitrix:desktop',
	'',
	Array(
		'ID' => 'mainpage_'.SITE_ID,
		'CAN_EDIT' => 'N',
		'COLUMNS' => '3',
		'COLUMN_WIDTH_0' => '260px',
		'COLUMN_WIDTH_1' => '',
		'COLUMN_WIDTH_2' => '260px',
		'G_ADV_TYPE' => 'INFO',
		'GADGETS' => Array('ALL'),
		'G_VIDEO_WIDTH'=>'400',
		'G_VIDEO_HEIGHT'=>'300',
		'G_VIDEO_CACHE_TYPE'=>'A',
		'G_VIDEO_LIST_URL'=>SITE_DIR.'about/media.php',
		'G_VIDEO_CACHE_TIME'=>'3600',
		'G_VOTE_CHANNEL_SID'=> Intranet\Integration\Wizards\Portal\Ids::getVoteChannelSid(SITE_ID),
		'G_VOTE_CACHE_TYPE'=>'A',
		'G_VOTE_CACHE_TIME'=>'3600',
		'G_VOTE_LIST_URL' => SITE_DIR.'services/votes.php',
		'G_BIRTHDAY_STRUCTURE_PAGE'=>'structure.php',
		'G_BIRTHDAY_PM_URL'=>SITE_DIR.'company/personal/messages/chat/#USER_ID#/',
		'G_BIRTHDAY_SHOW_YEAR'=>'M',
		'G_BIRTHDAY_USER_PROPERTY' => Array('WORK_POSITION'),
		'G_BIRTHDAY_LIST_URL'=>SITE_DIR.'company/birthdays.php',
		'G_LIFE_IBLOCK_TYPE'=>'news',
		'G_LIFE_DETAIL_URL'=>SITE_DIR.'about/life.php?ID=#ELEMENT_ID#',
		'G_LIFE_CACHE_TYPE'=>'A',
		'G_LIFE_CACHE_TIME'=>'3600',
		'G_LIFE_LIST_URL'=>SITE_DIR.'about/life.php',
		'G_OFFICIAL_IBLOCK_TYPE'=>'news',
		'G_OFFICIAL_DETAIL_URL'=>SITE_DIR.'about/official.php?ID=#ELEMENT_ID#',
		'G_OFFICIAL_CACHE_TYPE'=>'A',
		'G_OFFICIAL_CACHE_TIME'=>'3600',
		'G_OFFICIAL_LIST_URL'=>SITE_DIR.'about/',
		'G_SHARED_DOCS_IBLOCK_TYPE'=>'library',
		'G_SHARED_DOCS_IBLOCK_ID'=>Intranet\Integration\Wizards\Portal\Ids::getIblockId('shared_files'),
		'G_SHARED_DOCS_DETAIL_URL'=>SITE_DIR.'docs/shared/element/view/#ELEMENT_ID#/',
		'G_SHARED_DOCS_CACHE_TYPE'=>'A',
		'G_SHARED_DOCS_CACHE_TIME'=>'3600',
		'G_SHARED_DOCS_LIST_URL'=>SITE_DIR.'docs/',
		'G_COMPANY_CALENDAR_IBLOCK_TYPE'=>'events',
		'G_COMPANY_CALENDAR_IBLOCK_ID'=>Intranet\Integration\Wizards\Portal\Ids::getIblockId('calendar_company', SITE_ID),
		'G_COMPANY_CALENDAR_DETAIL_URL'=>SITE_DIR.'about/calendar.php',
		'G_COMPANY_CALENDAR_CACHE_TIME'=>'3600000',
		'G_PHOTOS_IBLOCK_TYPE'=>'photos',
		'G_PHOTOS_DETAIL_URL'=>SITE_DIR.'about/gallery/#SECTION_ID#/#ELEMENT_ID#/',
		'G_PHOTOS_DETAIL_SLIDE_SHOW_URL'=>SITE_DIR.'about/gallery/#SECTION_ID#/#ELEMENT_ID#/slide_show/',
		'G_PHOTOS_CACHE_TYPE'=>'A',
		'G_PHOTOS_CACHE_TIME'=>'3600000',
		'G_PHOTOS_LIST_URL'=>SITE_DIR.'about/gallery/',
		'G_WORKGROUPS_GROUP_VAR'=>'group_id',
		'G_WORKGROUPS_PATH_TO_GROUP'=>SITE_DIR.'workgroups/group/#group_id#/',
		'G_WORKGROUPS_PATH_TO_GROUP_SEARCH'=>SITE_DIR.'workgroups/',
		'G_WORKGROUPS_CACHE_TIME'=>'180',
		'G_BLOG_PATH_TO_BLOG'=>SITE_DIR.'company/personal/user/#user_id#/blog/',
		'G_BLOG_PATH_TO_POST'=>SITE_DIR.'company/personal/user/#user_id#/blog/#post_id#/',
		'G_BLOG_PATH_TO_GROUP_BLOG_POST'=>SITE_DIR.'workgroups/group/#group_id#/blog/#post_id#/',
		'G_BLOG_PATH_TO_USER'=>SITE_DIR.'company/personal/user/#user_id#/',
		'G_BLOG_CACHE_TYPE'=>'A',
		'G_BLOG_CACHE_TIME'=>'180',
		'G_TASKS_IBLOCK_ID'=>'#TASKS_IBLOCK_ID#', //deprecated
		'G_TASKS_PATH_TO_GROUP_TASKS'=>SITE_DIR.'workgroups/group/#group_id#/tasks/',
		'G_TASKS_PATH_TO_GROUP_TASKS_TASK'=>SITE_DIR.'workgroups/group/#group_id#/tasks/task/#action#/#task_id#/',
		'G_TASKS_PATH_TO_USER_TASKS'=>SITE_DIR.'company/personal/user/#user_id#/tasks/',
		'G_TASKS_PATH_TO_USER_TASKS_TASK'=>SITE_DIR.'company/personal/user/#user_id#/tasks/task/#action#/#task_id#/',
		'G_TASKS_IBLOCK_ID'=>'#TASKS_IBLOCK_ID#', //deprecated
		'G_TASKS_PATH_TO_GROUP_TASKS'=>SITE_DIR.'workgroups/group/#group_id#/tasks/',
		'G_TASKS_PATH_TO_GROUP_TASKS_TASK'=>SITE_DIR.'workgroups/group/#group_id#/tasks/task/#action#/#task_id#/',
		'G_TASKS_PATH_TO_USER_TASKS'=>SITE_DIR.'company/personal/user/#user_id#/tasks/',
		'G_TASKS_PATH_TO_USER_TASKS_TASK'=>SITE_DIR.'company/personal/user/#user_id#/tasks/task/#action#/#task_id#/',
		'G_CALENDAR_DETAIL_URL'=>SITE_DIR.'company/personal/user/#user_id#/calendar/',
		'G_CALENDAR_CACHE_TYPE'=>'N',
		'G_CALENDAR_CACHE_TIME'=>'3600000',
		'G_HONOUR_LIST_URL' => SITE_DIR.'company/leaders.php',
		'G_NEW_EMPLOYEES_LIST_URL' => SITE_DIR.'company/events.php',
	),
	false
);?><?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');