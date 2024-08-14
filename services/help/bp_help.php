<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/intranet/public/services/help/bp_help.php');
$APPLICATION->SetTitle(GetMessage('SERVICES_TITLE'));
echo GetMessage('SERVICES_INFO', array('#SITE#' => SITE_DIR));

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
