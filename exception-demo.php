<?php
declare(strict_types=1);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
function sum(float $a, float $b): int
{
    return $a + $b;
}

$a = 1;
$b = 2;
echo sum($a, $b);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
