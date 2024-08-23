# Тестовое задание
## Пункт 1
Решение на странице /stream/index.php и в компоненте aholin:department.head.list
## Пункт 2
Решение в файле to-d7.php
```php
\Bitrix\Main\Loader::includeModule('crm');
$dealFields = [
    'TITLE' => 'Тестовая сделка',
    'STAGE_ID' => 'C2:PREPARATION',
];
$idDeal = 15;
$updateResult = \Bitrix\Crm\DealTable::update($idDeal, $dealFields);

if ($updateResult->isSuccess()) {
    echo 'Сделка успешно обновлена';
} else {
    $errorMessage = 'Ошибка обновления: ';
    foreach ($updateResult->getErrorMessages() as $error) {
        $errorMessage .= $error . '<br>';
    }
    echo $errorMessage;
}

```
## Пункт 3
Решение на странице /new_deal_ids.php и в компоненте aholin:deal.grid

__Словесное описание решения:__
1. Компонент использующий список списка main.ui.grid
2. При вызове компонента передать параметр нужной стадии
3. При желании стадию можно вынести в b_option или в модуль настроек проекта, если такой ведётся
