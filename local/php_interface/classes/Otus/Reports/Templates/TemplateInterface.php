<?php
namespace Otus\Reports\Templates;
interface TemplateInterface
{
    public function __construct(string $templatePath);
    public function generate();
    public function makeFile(string $type);
    public function setData(array $data);
    function createFile();
}
