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

    <?php if (!$arResult['IS_NEW']) { ?>
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
            <div class="vacation-request__info" id="vacation_type"><strong>
                <?php Loc::getMessage('VACATION_TYPE_HINT')?>
            </strong> <?= $arResult['REQUEST_FIELDS']['vacation_type_label'] ?>
            </div>
            <div class="vacation-request__info" id="vacation_description"><strong><?=Loc::getMessage('DESCRIPTION_NOTE_HINT')?></strong>
                <?= $arResult['REQUEST_FIELDS']['description'] ?>
            </div>
            <?php if ($arResult['is_change_request']): ?>
            <div class="vacation-request__info" id="vacation_description"><strong><?=Loc::getMessage('CHANGED_VACATION_ITEM')?></strong>
                <br>
                <?= !empty($arResult['changed_range']) ? $arResult['changed_range'] : 'отпуска удалены' ?>
            </div>
            <?php endif;?>
        </div>
    <?php } else { ?>
        <div class="vacation-request__wrapper">
            <div class="vacation-request__info" id="vacation_initiator"><strong><?=Loc::getMessage('EMPLOYEE_HINT')?></strong><br/>
                <select id="REQUESTED_USER">
                    <option value=""></option>
                    <?php foreach ($arResult['USER_LIST'] as $user) { ?>
                        <option <?= $arResult['CURRENT_USER'] == $user['id'] ? 'selected' : '' ?>
                                value="<?= $user['id'] ?>"><?= $user['full_name'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="vacation-request__info" id="vacation_type"><strong><?=Loc::getMessage('VACATION_TYPE_HINT')?></strong><br/>
                <select id="VACATION_TYPE">
                    <option value=""></option>
                    <?php foreach ($arResult['VACATION_TYPE'] as $vacation) { ?>
                        <option value="<?= $vacation['id'] ?>"><?= $vacation['name'] ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <script>
            BX.ready(function () {
                BX.Plugin.UiSelector.createTagSelector('REQUESTED_USER')

                BX.Plugin.UiSelector.createTagSelector('VACATION_TYPE', {
                    tabs: [
                        {id: 'base', title: '<?=Loc::getMessage('VACATION_TYPE_HINT')?>', itemOrder: {id: 'asc'}},
                    ]
                })
            })
        </script>
    <?php } ?>

    <div class="vacation-request__wrapper">
        <div class="form-section">
            <div class="form-field" style="margin: 1% .7%;">
                <div id="VACATION_PERIODS_BLOCK">
                    <div id="vacation_periods_errors"></div>
                    <div id="vacation_periods_elements"></div>
                    <?php if ($arResult["permissions"]["can_edit"] || empty($arParams["REQUEST_ID"])) { ?>
                        <div class="create-button__wrapper">
                            <span class="ui-btn ui-btn-primary ui-btn-icon-add vacation-period-add"
                                  onclick="BX.Otus.Vacation.Request.createVacation()"><?=Loc::getMessage('ADD_PERIOD_BUTTON_TITLE')?></span>
                        </div>
                        <?php } ?>
                    <div <?= !empty($arParams["REQUEST_ID"]) ? 'style="display:none;"' : '' ?> hidden class="vacation-days_count" id="vacation_days_count"></div>
                    <input id="input_periods" type="hidden" name="PLANNED_VACATION_PERIODS" value="">
                </div>
            </div>
        </div>
        
        <?php if ($arResult["permissions"]["can_edit"]) { ?>
            <div class="description__wrapper">
                <div class="ui-slider-heading-1"><?=Loc::getMessage('COMMENT_TITLE')?></div>
                <textarea name="description" id="vacation_request_description" cols="30" rows="10"
                class="ui-ctl-textarea"></textarea>
            </div>
        <?php } ?>
    </div>
    <?php if (!empty($arResult["approval_log"])) {?>
    <div class="vacation-request__wrapper">
        <div class="form-section__title">
            <?=Loc::getMessage('APPROVAL_LOG_LINK')?>
        </div>
        <div class="approval__comments">
        <?php 
        foreach ($arResult["approval_log"] as $log) {
            ?>
                <div class="feed-com-block-cover">
                    <div class="feed-com-block-outer">
                        <div class="feed-com-block blog-comment-user-1 sonet-log-comment-createdby-1 feed-com-block-approved">
                            <div class="ui-icon ui-icon-common-user feed-com-avatar<?php if (empty($log["USER_PHOTO"])):?> feed-com-avatar-N <?php endif;?>">
                                <i></i>
                                <?php if (!empty($log["USER_PHOTO"])) {?>
                                <img src="<?=$log["USER_PHOTO"]?>">
                                <?php }?>
                            </div>
                            <div class="feed-com-main-content feed-com-block-old">
                                <div class="feed-com-user-box">
                                    <a target="_top" class="feed-com-name feed-author-name feed-author-name-1" bx-tooltip-user-id="1"
                                    bx-tooltip-params="[]" href="/company/personal/user/<?=$log["APPROVAL_ID"]?>/">
                                        <?=$log["APPROVAL_NAME"]?>
                                    </a>
                                    <a class="feed-time feed-com-time" rel="nofollow" target="_top">
                                        <?=$log["COMMENT_TIME"]?>
                                    </a>
                                </div>
                                <div class="feed-com-text">
                                    <div class="feed-com-text-inner">
                                        <?=$log["DESCRIPTION"]?>
                                        <br>
                                        <?php
                                            $status = '';
                                            $className = '';

                                            switch ($log["TYPE"]) {
                                                case 'REJECTED':
                                                    $className = 'red-reject';
                                                    $status = 'ОТКЛОНЕНА';
                                                    break;
                                                case 'REVISION':
                                                    $className = 'blue-alternative';
                                                    $status = 'ПРЕДЛОЖЕНЫ ИЗМЕНЕНИЯ';
                                                    break;
                                                case 'AGREED':
                                                    $className = 'green-approve';
                                                    $status = 'СОГЛАСОВАНО';
                                                    break;
                                                default:
                                                    $className = 'undefined-log';
                                                    $status = 'НЕИЗВЕСТНЫЙ СТАТУС';
                                                    break;
                                            }
                                        ?>
                                        <span class="decision">Решение:</span> <span class="<?=$className?>"><?=$status?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
        }
        ?>
        </div>
    </div>
    <div class="vacation-request__wrapper">
    </div>
    <?php }?>
</div>
<div class="ui-entity-wrap crm-section-control-active bottom-buttons">
    <div class="ui-entity-section ui-entity-section-control">
        <div class="ui-entity-section ui-entity-section-control-edit-mode"
             style="display: flex;justify-content: center;">
            <?php if ($arResult["permissions"]["can_start_approval"]) { ?>
                <button class="ui-btn" onclick="BX.Otus.Vacation.Request.startVacationRequestApproval()">
                    <?=Loc::getMessage('START_APPROVAL_BUTTON_TITLE')?>
                </button>
            <?php } ?>
            <?php if ($arResult["permissions"]["can_edit"]) { ?>
                <button class="ui-btn ui-btn-success" onclick="BX.Otus.Vacation.Request.save()">
                <?=Loc::getMessage('SAVE_BUTTON_TITLE')?>
            </button>
            <?php } ?>
            <?php if ($arResult["permissions"]["can_approve"]) { ?>
                <button class="ui-btn ui-btn-success" onclick="BX.Otus.Vacation.Request.approveVacationRequest()">
                    <?=Loc::getMessage('APPROVE_BUTTON_TITLE')?>
                </button>
                <button class="ui-btn ui-btn-primary" onclick="BX.Otus.Vacation.Request.alternativeVacationRequest()">
                    <?=Loc::getMessage('ALTERNATIVE_BUTTON_TITLE')?>
                </button>
                <?php /*
                <button class="ui-btn ui-btn-danger" onclick="BX.Otus.Vacation.Request.rejectVacationRequest()">
                    <?=Loc::getMessage('REJECT_BUTTON_TITLE')?>
                </button>
                */?>
            <?php } ?>
            <a href="#" class="ui-btn ui-btn-link" onclick="BX.Otus.Vacation.Request.close()"><?=Loc::getMessage('CLOSE_BUTTON_TITLE')?></a></div>
        <div class="ui-entity-section ui-entity-section-control-view-mode" style="display: none;"></div>
        <div class="ui-entity-section-control-error-block" style="max-height: 0px;"></div>
    </div>
</div>
<script>
    BX.ready(function () {
        let params = {
            items: <?= \Bitrix\Main\Web\Json::encode($arResult["ITEMS"]); ?>,
            permissions: <?= \Bitrix\Main\Web\Json::encode($arResult['permissions']) ?>,
            vacationRequestId: <?= (int)$arParams["REQUEST_ID"] ?>,
            vacationRequestDescription: "<?= $arResult["VACATION_REQUEST_DESCRIPTION"]?>",
            absenceData: <?= \Bitrix\Main\Web\Json::encode($arResult['ABSENCE_DATA']) ?>,
            approvalLog: <?= \Bitrix\Main\Web\Json::encode($arResult['approval_log']) ?>,
            currentDate: "<?= $arResult["CURRENT_DATE"] ?>",
        }

        BX.ready(function () {
            BX.Otus.Vacation.Request.init(params)
        })
    })
</script>