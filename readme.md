# Комментарии по ДЗ
## Связывание моделей
### Требование
#### ДЗ от 26.08
Загружены два новых файла
fetchall-query.php и collection-query.php
с примерами запросов для получение полей и 
свойств через множественное свойство типа "Привязка к элемениу инфоблока"
## Урок 5 Задание 2
### Требование
#### Написать и подключить собственный класс системного логгера, который будет переопределять форматирование строк лога - добавлять слово OTUS в каждую строку.
Пример настроенного файла
_.settings.php_
```php
<?php
return [
/** 
 * другие параметры
 */
  'exception_handling' =>
  [
    'value' =>
    [
      'debug' => true,
      'handled_errors_types' => 4437,
      'exception_errors_types' => 4437,
      'ignore_silence' => false,
      'assertion_throws_exception' => true,
      'assertion_error_type' => 256,
      'log' => [
          'class_name' => 'Otus\\Diagnostic\\OtusFileExceptionHandlerLog',
          'required_file' => 'php_interface/classes/Helper/OtusFileExceptionHandlerLog.php', // путь относительно папки local
          'settings' =>
              [
                  'file' => 'logs/otus_exceptions.log', // путь относительно папки сайта
                  'log_size' => 1000000,
              ],
      ],
    ],
    'readonly' => false,
  ],
/** 
 * другие параметры
 */ 
];
```