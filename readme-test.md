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
$updateResult = \Aholin\Crm\DealTable::update($idDeal, $dealFields); # переопределил класс

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
_Дополнение_ 

Добавил обработчик события который меняет стадию C2:PREPARATION на 'NEW'
### Жизненный цикл обновления сделки
- Получение текущих данных
- Проверка обязательных полей
- Проверка прав на изменение
- Вызов события OnBeforeCrmDealUpdate, если опция ENABLE_SYSTEM_EVENTS не определена в false
- Подготовка технических полей (стадий, дат обновлений, реляций).
- Проведения сравнения с предыдущими значениями (при необходимости)
- Запись изменений в таблицу
- Сохранение пользовательских полей
- Расчет прав
- Перепривязка контактов
- Сохранение наблюдателей
- Классификация сделки
- Завершение дел, при закрытии сделки
- Запись в статистические таблицы и историю
- Обновление UTM полей
- Обновление поискового индекса
- Запись изменений в timeline
- Запись сообщения в ленту сделки/чат
- Вызов события OnAfterCrmDealUpdate, если опция ENABLE_SYSTEM_EVENTS не определена в false
- Если доступно ML (Machine learning) и это не системное действие отправка в ML
- Отправка push-сообщений для обновления канбана и визуализации добавления элемента
## Пункт 3
Решение на странице /new_deal_ids.php и в компоненте aholin:deal.grid

__Словесное описание решения:__
1. Компонент использующий список списка main.ui.grid
2. При вызове компонента передать параметр нужной стадии
3. При желании стадию можно вынести в b_option или в модуль настроек проекта, если такой ведётся

_Дополнение_

Ответил в переписке на github