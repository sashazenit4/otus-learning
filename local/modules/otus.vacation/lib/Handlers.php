<?php

namespace Otus\Vacation;

use Bitrix\Main\Context;
use Bitrix\Main\UI\Extension;

class Handlers
{
	public static function handleSidepanelLinks()
	{
		$request = Context::getCurrent()->getRequest();

		if ( $request->isAjaxRequest() )
		{
			return true;
		}

		Extension::load([
			"otus.vacation_sidepanel_handler"
		]);
	}
}