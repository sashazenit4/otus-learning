<?php
namespace Otus\Reports\Templates;

abstract class AbstractTemplate implements TemplateInterface
{
    protected string $prefix = 'template_';
    protected string $filePath;
    protected array $data = [];
    protected DocxTemplateProcessor $docProcessor;
    const DATA_APPLICATION_DOCX = 'data:application/vnd.openxmlformats-officedocument.wordprocessingml.document;base64,';
    const DATA_APPLICATION_PDF = 'data:application/pdf;base64,';
    public function __construct(protected readonly string $templatePath)
    {
        $this->generate();
    }
    public function generate(): void
    {
        try {
            // Создание объекта на основе шаблона
            $this->docProcessor = new DocxTemplateProcessor($this->templatePath);
        } catch (\Exception $e) {
            echo 'Произошла ошибка: ' . $e->getMessage();
        }
    }
    public function createFile(): void
    {
        $outputPath = $this->generateTempFilePath();
        $this->docProcessor->save($outputPath);
        $this->filePath = $outputPath;
    }
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function makeFile(string $type = 'docx'): string
    {
        return $this->filePath ?? '';
    }

    protected function generateTempFilePath(): string
    {
        return sys_get_temp_dir() .'/' . $this->prefix . date('d.m.Y');
    }
}
