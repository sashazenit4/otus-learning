<?php

namespace Otus\Vacation;

abstract class BaseReport implements ReportInterface
{
    const BASE_NAME = 'отпуска';

    const DOCUMENT_FORMAT = '';

    const DATA_APPLICATION = '';

    protected $title = null;

    protected $filter = [];

    protected $errors = [];

    protected $writer;

    protected $cachedData = [];

    public $additionalParams = [];

    /**
     * @inheritdoc
     */
    protected function setError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * @inheritDoc
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @inheritDoc
     */
    public function getReportId(): string
    {
        return strtoupper(str_replace('\\', '_', static::class));
    }

    /**
     * @inheritDoc
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @inheritDoc
     */
    public function setFilter(array $filter = [])
    {
        $this->filter = $filter;
    }

    /**
     * @inheritDoc
     */
    public function getReportFormat(): string
    {
        return static::DOCUMENT_FORMAT;
    }

    /**
     * @inheritDoc
     */
    public function getDataApplication(): string
    {
        return static::DATA_APPLICATION;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->title ?? static::BASE_NAME;
    }

    /**
     * @inheritDoc
     */
    public function getFilterParams(): ?array
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getCustomStyleHtml(): ?string
    {
        $style = "";

        return $style;
    }

    /**
     * @inheritDoc
     */
    protected function validateFilter(array $filter = []): bool
    {
        $isValid = true;

        if (isset($filter['CUST_ERROR']) && $filter['CUST_ERROR'] === true)
        {
            $this->setError("Невозможно вывести данные с текущими условиями!<br>Укажите в фильтре другой параметр.");
            $isValid = false;
        }

        $reportFilterParams = $this->getFilterParams();

        if (!empty($reportFilterParams)) {
            $errors = [];

            $requiredFilterFields = array_filter($reportFilterParams['FILTER'], function ($f) {
                return $f['require'] == true;
            });

            $filter = array_filter($filter);

            $filterKeys = array_unique(array_map(function ($k) {
                return str_replace(['>', '<', '='], '', $k);
            }, array_keys($filter)));

            foreach ($requiredFilterFields as $requiredFilterField) {
                if (!in_array($requiredFilterField['id'], $filterKeys)) {
                    $this->setError('Не указан параметр в фильтре "' . $requiredFilterField['name'] . '"');
                    $isValid = false;
                }
            }
        }

        return $isValid;
    }

    /**
     * @inheritDoc
     */
    public function getCachedData()
    {
        return $this->cachedData;
    }

    /**
     * @inheritDoc
     */
    public function prepareTitle(array $params = []): string
    {
        return $this->getName();
    }
}