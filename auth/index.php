<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/auth/index.php");

if (isset($_REQUEST["backurl"]) && is_string($_REQUEST["backurl"]) && mb_strpos($_REQUEST["backurl"], "/") === 0)
{
	LocalRedirect($_REQUEST["backurl"]);
}

$APPLICATION->SetTitle(GetMessage("AUTH_TITLE"));
?>
<p><?=GetMessage("AUTH_SUCCESS")?></p>

<p><a href="<?=SITE_DIR?>"><?=GetMessage("AUTH_BACK")?></a></p>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>