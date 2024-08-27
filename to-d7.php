<?php
include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Crm\Service\Container;

\Bitrix\Main\Loader::includeModule('crm');

$idDeal = 9;

$dealFactory = Container::getInstance()->getFactory(\CCrmOwnerType::Deal);

$dealItem = $dealFactory->getItem($idDeal);

$dealItem->set('TITLE', 'Тестовая сделка');
$dealItem->set('STAGE_ID', 'C2:PREPARATION');

$operation = $dealFactory->getUpdateOperation($dealItem);

$updateResult = $operation->launch();

if ($updateResult->isSuccess()) {
    echo 'Сделка успешно обновлена';
} else {
    $errorMessage = 'Ошибка обновления: ';
    foreach ($updateResult->getErrorMessages() as $error) {
        $errorMessage .= $error . '<br>';
    }
    echo $errorMessage;
}
