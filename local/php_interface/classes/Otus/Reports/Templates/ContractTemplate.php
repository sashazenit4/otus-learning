<?php
namespace Otus\Reports\Templates;

class ContractTemplate extends AbstractTemplate implements TemplateInterface
{
    public function generate(): void
    {
        try {
            // Создание объекта на основе шаблона
            $this->docProcessor = new DocxTemplateProcessor($this->templatePath);
            foreach ($this->data as $dataKey => $dataValue) {
                $this->docProcessor->replaceText($dataKey, $dataValue);
            }
        } catch (\Exception $e) {
            echo "Произошла ошибка: " . $e->getMessage();
        }
    }
    public function setPrefix($prefix): void
    {
        $this->prefix = $prefix;
    }
}
