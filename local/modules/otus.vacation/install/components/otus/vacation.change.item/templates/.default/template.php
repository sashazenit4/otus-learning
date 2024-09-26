<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;

$APPLICATION->setTitle($arResult['TITLE']);

Extension::load([
    'jquery',
    'ui.sidepanel-content',
    'ui',
    'otus.vacation',
    'otus.ui-selector',
    'ui.forms',
    'ui.alerts',
    'ui.icons'
]);

Loc::loadMessages(__FILE__);
?>
<div class="task__wrapper vacation-form">
    <div>
        <a href="/vacation_schedule/" target="_blank" class='right blue-alternative item__underline'><?=Loc::getMessage('VACATION_SCHEDULE_LINK')?></a>
    </div>

    <div class="vacation-request__wrapper">
        <div class="form-section__title">
            <?=Loc::getMessage('EMPLOYEE_HINT')?>
        </div>
        <div class="vacation-request__info" id="vacation_initiator">
            <?php $APPLICATION->IncludeComponent("bitrix:main.user.link", "", array(
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "7200",
                "ID" => $arResult['REQUEST_FIELDS']['requested_user_id'],
                "NAME_TEMPLATE" => "#NOBR##LAST_NAME# #NAME##/NOBR#",
                "SHOW_LOGIN" => "Y",
                "THUMBNAIL_LIST_SIZE" => "30",
                "THUMBNAIL_DETAIL_SIZE" => "100",
                "USE_THUMBNAIL_LIST" => "Y",
                "SHOW_FIELDS" => array("WORK_POSITION"),
            ),
            ); ?>
        </div>
        <br>
        <div class="form-section__title">
            <?=Loc::getMessage('VACATION_ITEM_CHOICE_LABEL')?>
        </div>
        <div class="vacation-request__info" id="vacation_initiator">
            <select name="vacation_item[]" multiple id="vacation_item">
            <?php foreach ($arResult['APPROVED_VACATION_ITEMS'] as $vacationItemId => $vacationItem): ?>
                <option value="<?=$vacationItemId?>"><?=$vacationItem?></option>
            <?php endforeach;?>
            </select>
            <select name="vacation_item_types[]" style="display: none;" id="vacation_item_types">
            <?php foreach($arResult['APPROVED_VACATION_ITEMS_TYPES'] as $vacationItemId => $vacationItemType):?>
                <option value="<?=$vacationItemId?>"><?=$vacationItemType?></option>
            <?php endforeach;?>
            </select>
        </div>
        <script>
            BX.ready(function () {
                BX.Plugin.UiSelector.createTagSelector('vacation_item')
            })
        </script>
        <br>
        <div class="form-section__title">
            <?=Loc::getMessage('VACATION_ITEM_DATEPICKER_LABEL')?>
        </div>
        <div class="form-section">
            <div class="form-field" style="margin: 1% .7%;">
                <div id="VACATION_PERIODS_BLOCK">
                    <div id="vacation_periods_errors"></div>
                    <div id="vacation_periods_elements"></div>
                    <div class="create-button__wrapper">
                        <span class="ui-btn ui-btn-primary ui-btn-icon-add vacation-period-add"
                            onclick="BX.Otus.Vacation.Change.createVacation()"><?=Loc::getMessage('ADD_PERIOD_BUTTON_TITLE')?></span>
                    </div>
                    <input id="input_periods" type="hidden" name="PLANNED_VACATION_PERIODS" value="">
                </div>
            </div>
        </div><br>
        <div class="form-section__title">
            <?=Loc::getMessage('VACATION_CHANGE_ITEM_DESCRIPTION_LABEL')?>
        </div>
        <div class="form-section">
            <div class="description__wrapper">
                <textarea name="description" id="vacation_request_description" cols="30" rows="10" class="ui-ctl-textarea"></textarea>
            </div>
        </div>
    </div>
</div>
<div class="ui-entity-wrap crm-section-control-active bottom-buttons">
    <div class="ui-entity-section ui-entity-section-control">
        <div class="ui-entity-section ui-entity-section-control-edit-mode"
             style="display: flex;justify-content: center;">
            <button class="ui-btn" onclick="BX.Otus.Vacation.Change.startVacationRequestApproval()">
                <?=Loc::getMessage('START_APPROVAL_BUTTON_TITLE')?>
            </button>
            <button class="ui-btn ui-btn-success" onclick="BX.Otus.Vacation.Change.save()">
                <?=Loc::getMessage('SAVE_BUTTON_TITLE')?>
            </button>
            <a href="#" class="ui-btn ui-btn-link" onclick="BX.Otus.Vacation.Change.close()"><?=Loc::getMessage('CLOSE_BUTTON_TITLE')?></a></div>
        <div class="ui-entity-section ui-entity-section-control-view-mode" style="display: none;"></div>
        <div class="ui-entity-section-control-error-block" style="max-height: 0px;"></div>
    </div>
</div>
<script>
    BX.ready(() => {
        let params = {
            absenceData: <?= \Bitrix\Main\Web\Json::encode($arResult['ABSENCE_DATA']) ?>,
        }

        BX.Otus.Vacation.Change.init(params)
    })
</script>