<?php
namespace Otus\Reports\Templates;

use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\TemplateProcessor;

final class DocxTemplateProcessor
{
    private TemplateProcessor $templateProcessor;

    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     * @throws \Exception
     */
    public function __construct($templatePath)
    {
        if (!file_exists($templatePath)) {
            throw new \Exception("Файл шаблона не найден: $templatePath");
        }

        // Инициализация TemplateProcessor с заданным шаблоном
        $this->templateProcessor = new TemplateProcessor($templatePath);
    }

    /**
     * Метод для замены текста в шаблоне
     *
     * @param string $search Текст для поиска в шаблоне
     * @param string $replace Текст, на который нужно заменить
     */
    public function replaceText(string $search, string $replace): void
    {
        $this->templateProcessor->setValue($search, $replace);
    }

    /**
     * Сохранение измененного документа
     *
     * @param string $outputPath Путь для сохранения нового документа
     */
    public function save(string $outputPath): void
    {
        $this->templateProcessor->saveAs($outputPath);
    }
}
