<?php

namespace Otus\Vacation;

interface ReportInterface
{
    /**
     * Получение ошибок
     * @return array
     */
    public function getErrors(): array;

    /**
     * Полученеи ID отчёта
     * @return string
     */
    public function getReportId(): string;

    /**
     * Установка названия отчёта
     * @param string $title
     * @return mixed
     */
    public function setTitle(string $title);

    /**
     * Установка фильтра
     * @param array $filter
     */
    public function setFilter(array $filter = []);

    /**
     * Получение формата генерируемого отчёта
     * @return string
     */
    public function getReportFormat(): string;

    /**
     * Получение заголовка для скачивания
     * @return string
     */
    public function getDataApplication(): string;

    /**
     * Генерация документа
     * @return bool
     */
    public function generateDocument(): bool;

    /**
     * Формирование документа
     * @return string
     */
    public function makeResultFile(string $outputFile): string;

    /**
     * Формирование Html документа
     * @return string
     */
    public function makeHtmlFile(string $outputFile): string;

    /**
     * Получение названия отчёта
     * @return string
     */
    public function getName(): string;

    /**
     * Получение параметров фильтрации
     * @return array|null
     */
    public function getFilterParams(): ?array;

    /**
     * Получение кастомных стилей для страницы отчёта
     * @return string|null
     */
    public function getCustomStyleHtml(): ?string;

    /**
     * Получение кэшированных данных
     * @return mixed
     */
    public function getCachedData();

    /**
     * Подготовка заголовка для отчёта
     * @param array $params
     * @return string
     */
    public function prepareTitle(array $params = []): string;
}