<?php declare(strict_types=1);
namespace rekdagyothub;

include_once __DIR__ . '/../../vendor/autoload.php';
foreach (XlsxReader::iterateRecords(__DIR__ . '/../../files/2023/Studierende Muwi WS 1927 28 Personen_MWA_5.10._ (4).xlsx') as $v) {
    echo implode(PHP_EOL, array_filter(array_keys($v), fn($col) => $col !== '::marked'));
    break;
}
