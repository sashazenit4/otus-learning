<?

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$aMenuLinks = Array(
	Array(
		Loc::getMessage('LEFT_MENU__VACATION_REGISTRY_TITLE'),
		"/",
		Array(), 
		Array(), 
		"" 
	),
    Array(
        Loc::getMessage('LEFT_MENU__VACATION_TITLE'),
        "/vacation_request/",
        Array(),
        Array(),
        ""
    ),
);
?>