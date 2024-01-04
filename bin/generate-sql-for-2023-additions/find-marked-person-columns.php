<?php declare(strict_types=1);
namespace rekdagyothub;

include_once __DIR__ . '/../../vendor/autoload.php';
$markedColumns = [];
foreach (XlsxReader::iterateRecords(__DIR__ . '/../../files/2023/Studierende Muwi WS 1927 28 Personen_MWA_5.10._ (4).xlsx') as $v) {
    if (!empty($v['::marked'])) {
        $markedColumns = array_merge($markedColumns, $v['::marked']);
    }
}
echo implode(PHP_EOL, array_unique($markedColumns));
