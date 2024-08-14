<?php
/**
 * @global  \CMain $APPLICATION
 * @global \CUser $USER
 */
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/intranet/public/workgroups/extranet/.left.menu_ext.php');

// You can change this url template
$strGroupSubjectLinkTemplate = COption::GetOptionString('socialnetwork', 'subject_path_template', SITE_DIR.'workgroups/group/search/#subject_id#/');
$strGroupLinkTemplate = COption::GetOptionString('socialnetwork', 'group_path_template', SITE_DIR.'workgroups/group/#group_id#/');

if (SITE_TEMPLATE_ID == 'bitrix24')
{
	if (CModule::IncludeModule('extranet') && CBXFeatures::IsFeatureEnabled('Workgroups') && CBXFeatures::IsFeatureEnabled('Extranet')):
		$USER_ID = $USER->GetID();
		$arExSGGroup = array();

		if (CModule::IncludeModule('socialnetwork'))
		{

			if (defined('BX_COMP_MANAGED_CACHE'))
			{
				global $CACHE_MANAGER;
				$CACHE_MANAGER->RegisterTag('sonet_user2group_U'.$USER_ID);
			}

			// get all groups from current site (if not extranet)
			if (SITE_ID != CExtranet::GetExtranetSiteID())
			{
				$arGroupForCheck = array();

				$dbGroups = CSocNetGroup::GetList(
					array(),
					array(
						'SITE_ID' => SITE_ID
					),
					false,
					false,
					array('ID')
				);

				while ($arGroups = $dbGroups->GetNext())
				{
					$arGroupForCheck[] = $arGroups['ID'];
				}
			}

			if (
				!is_array($arGroupForCheck)
				|| count($arGroupForCheck) > 0
			)
			{
				$arGroupFilterMy = array(
					'USER_ID' => $USER_ID,
					'<=ROLE' => SONET_ROLES_USER,
					'GROUP_ACTIVE' => 'Y',
					'!GROUP_CLOSED' => 'Y',
					'GROUP_SITE_ID' => CExtranet::GetExtranetSiteID()
				);

				if (count($arGroupForCheck) > 0)
				{
					$arGroupFilterMy['GROUP_ID'] = $arGroupForCheck;
				}

				// Socialnetwork
				$dbGroups = CSocNetUserToGroup::GetList(
					array('GROUP_NAME' => 'ASC'),
					$arGroupFilterMy,
					false,
					false,
					array('ID', 'GROUP_ID', 'GROUP_NAME')
				);

				while ($arGroups = $dbGroups->GetNext())
				{
					$arExSGGroup[] = array(
						$arGroups['GROUP_NAME'],
						str_replace('#group_id#', $arGroups['GROUP_ID'], $strGroupLinkTemplate),
						array(),
						array(/*'counter_id' => 'SG'.$arGroups['GROUP_ID']*/),
						''
					);
					if (defined('BX_COMP_MANAGED_CACHE'))
					{
						$CACHE_MANAGER->RegisterTag('sonet_group_'.$arGroups['ID']);
					}
				}
			}
		}
		$aMenuLinks = $arExSGGroup;
	endif;
}
else
{
	if (CModule::IncludeModule('socialnetwork'))
	{
		if (!function_exists('__CheckPath4Template'))
		{
			function __CheckPath4Template($pageTemplate, $currentPageUrl, &$arVariables)
			{
				$pageTemplateReg = preg_replace("'#[^#]+?#'", "([^/]+?)", $pageTemplate);

				$arValues = array();
				if (preg_match("'^".$pageTemplateReg."'", $currentPageUrl, $arValues))
				{
					$arMatches = array();
					if (preg_match_all("'#([^#]+?)#'", $pageTemplate, $arMatches))
					{
						for ($i = 0, $cnt = count($arMatches[1]); $i < $cnt; $i++)
							$arVariables[$arMatches[1][$i]] = $arValues[$i + 1];
					}
					return True;
				}

				return False;
			}
		}

		$arGroup = false;
		$arVariables = array();
		$componentPage = __CheckPath4Template($strGroupLinkTemplate, $_SERVER['REQUEST_URI'], $arVariables);
		if (
			$componentPage 
			&& intval($arVariables['group_id']) > 0
		)
		{
			$arGroup = CSocNetGroup::GetByID(intval($arVariables['group_id']));
		}

		$dbGroupSubjects = CSocNetGroupSubject::GetList(
			array('SORT' => 'ASC', 'NAME' => 'ASC'),
			array('SITE_ID' => SITE_ID),
			false,
			false,
			array('ID', 'NAME')
		);

		$aMenuLinksAdd = array();
		while ($arGroupSubject = $dbGroupSubjects->GetNext())
		{
			$arLinks = array();
			if ($arGroup && $arGroup['SUBJECT_ID'] == $arGroupSubject['ID'])
				$arLinks = array($_SERVER['REQUEST_URI']);

			$aMenuLinksAdd[] = array(
				$arGroupSubject['NAME'],
				str_replace('#subject_id#', $arGroupSubject['ID'], $strGroupSubjectLinkTemplate),
				$arLinks,
				array(),
				''
			);
		}

		$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksAdd);

		$aMenuLinks[] = array(GetMessage('WORKGROUPS_MENU_ARCHIVE'), str_replace('#subject_id#', -1, $strGroupSubjectLinkTemplate), array(), array(), '');
	}
}
