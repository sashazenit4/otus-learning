<?php

use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var $arResult
 */

$this->SetViewTarget('sidebar', 400);
$frame = $this->createFrame()->begin();

?>
<div class="sidebar-widget sidebar-widget-birthdays">
    <div class="sidebar-widget-top">
        <div class="sidebar-widget-top-title"><?=Loc::getMessage('DEPARTMENT_HEAD_TITLE')?></div>
    </div>
    <div class="sidebar-widget-content">
        <?php foreach ($arResult['EMPLOYEES'] as $employee) {?>
        <a href="/company/personal/user/<?=$employee['ID']?>/" class="sidebar-widget-item --row today-birth">
            <?php if (!isset($employee['PERSONAL_PHOTO'])) {?>
                <span class="user-avatar user-default-avatar" style=""></span>
            <?php } else { ?>
                <span class="user-avatar user-default-avatar" style="background: url('<?=$employee['PERSONAL_PHOTO']?>') no-repeat center; background-size: cover;"></span>
            <?php
            }?>
            <span class="sidebar-user-info">
				<span class="user-birth-name"><?=$employee['NAME'] . ' ' . $employee['LAST_NAME']?></span>
			</span>
        </a>
        <?php }?>
    </div>
</div>
<?php
$frame->end();
$this->EndViewTarget();
?>
