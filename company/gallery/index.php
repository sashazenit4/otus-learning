<?php
/**
 * @global  \CMain $APPLICATION
 */

use \Bitrix\Intranet;

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/intranet/public/company/gallery/index.php');
$APPLICATION->SetTitle(GetMessage('COMPANY_TITLE'));
\CModule::IncludeModule('intranet');

?><?php
$APPLICATION->IncludeComponent(
	'bitrix:photogallery_user', 
	'.default', [
		'SECTION_PAGE_ELEMENTS' => '100',
		'ELEMENTS_PAGE_ELEMENTS' => '100',
		'PAGE_NAVIGATION_TEMPLATE' => '',
		'ELEMENTS_USE_DESC_PAGE' => 'Y',
		'IBLOCK_TYPE' => 'photos',
		'IBLOCK_ID' => Intranet\Integration\Wizards\Portal\Ids::getIblockId('user_photogallery'),
		'GALLERY_GROUPS' => [
			0 => '13',
			1 => '1',
		],
		'ONLY_ONE_GALLERY' => 'Y',
		'SECTION_SORT_BY' => 'ID',
		'SECTION_SORT_ORD' => 'ASC',
		'ELEMENT_SORT_FIELD' => 'id',
		'ELEMENT_SORT_ORDER' => 'desc',
		'ANALIZE_SOCNET_PERMISSION' => 'Y',
		'UPLOAD_MAX_FILE_SIZE' => '64',
		'GALLERY_AVATAR_SIZE' => '50',
		'ALBUM_PHOTO_THUMBS_SIZE' => '150',
		'ALBUM_PHOTO_SIZE' => '150',
		'THUMBS_SIZE' => '250',
		'PREVIEW_SIZE' => '700',
		'JPEG_QUALITY1' => '95',
		'JPEG_QUALITY2' => '95',
		'JPEG_QUALITY' => '90',
		'WATERMARK_MIN_PICTURE_SIZE' => '200',
		'ADDITIONAL_SIGHTS' => [],
		'UPLOAD_MAX_FILE' => '1',
		'PATH_TO_FONT' => '',
		'SEF_MODE' => 'Y',
		'SEF_FOLDER' => SITE_DIR.'company/gallery/',
		'CACHE_TYPE' => 'A',
		'CACHE_TIME' => '3600',
		'DISPLAY_PANEL' => 'N',
		'SET_TITLE' => 'Y',
		'USE_RATING' => 'N',
		'DISPLAY_AS_RATING' => 'rating_main',
		'MAX_VOTE' => '5',
		'VOTE_NAMES' => [
			0 => '0',
			1 => '1',
			2 => '2',
			3 => '3',
			4 => '4',
		],
		'SHOW_TAGS' => 'N',
		'ORIGINAL_SIZE' => '1280',
		'UPLOADER_TYPE' => 'form',
		'USE_COMMENTS' => 'Y',
		'COMMENTS_TYPE' => 'forum',
		'FORUM_ID' => Intranet\Integration\Wizards\Portal\Ids::getForumId('PHOTOGALLERY_COMMENTS'),
		'PATH_TO_SMILE' => '/bitrix/images/blog/smile/',
		'URL_TEMPLATES_READ' => '',
		'USE_CAPTCHA' => 'N',
		'SHOW_LINK_TO_FORUM' => 'N',
		'PREORDER' => 'Y',
		'MODERATE' => 'N',
		'SHOW_ONLY_PUBLIC' => 'N',
		'WATERMARK_COLORS' => [
			0 => 'FF0000',
			1 => 'FFFF00',
			2 => 'FFFFFF',
			3 => '000000',
		],
		'TEMPLATE_LIST' => '.default',
		'CELL_COUNT' => '0',
		'SLIDER_COUNT_CELL' => '4',
		'SEF_URL_TEMPLATES' => [
			'index' => 'index.php',
			'galleries' => 'galleries/#USER_ID#/',
			'gallery' => SITE_DIR.'company/personal/user/#USER_ID#/photo/gallery/#USER_ALIAS#/',
			'gallery_edit' => SITE_DIR.'company/personal/user/#USER_ID#/photo/gallery/#USER_ALIAS#/action/#ACTION#/',
			'section' => SITE_DIR.'company/personal/user/#USER_ID#/photo/album/#USER_ALIAS#/#SECTION_ID#/',
			'section_edit' => SITE_DIR.'company/personal/user/#USER_ID#/photo/album/#USER_ALIAS#/#SECTION_ID#/action/#ACTION#/',
			'section_edit_icon' => SITE_DIR.'company/personal/user/#USER_ID#/photo/album/#USER_ALIAS#/#SECTION_ID#/icon/action/#ACTION#/',
			'upload' => SITE_DIR.'company/personal/user/#USER_ID#/photo/photo/#SECTION_ID#/action/upload/',
			'detail' => SITE_DIR.'company/personal/user/#USER_ID#/photo/photo/#USER_ALIAS#/#SECTION_ID#/#ELEMENT_ID#/',
			'detail_edit' => SITE_DIR.'company/personal/user/#USER_ID#/photo/photo/#USER_ALIAS#/#SECTION_ID#/#ELEMENT_ID#/action/#ACTION#/',
			'detail_slide_show' => SITE_DIR.'company/personal/user/#USER_ID#/photo/photo/#USER_ALIAS#/#SECTION_ID#/#ELEMENT_ID#/slide_show/',
			'detail_list' => 'list/',
			'search' => 'search/',
			'tags' => 'tags/',
		],
	]
);
?><?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
