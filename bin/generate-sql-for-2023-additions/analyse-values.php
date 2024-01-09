<?php declare(strict_types=1);
namespace rekdagyothub;

include_once __DIR__ . '/../../vendor/autoload.php';
function shorten(string $string, int $length)
{
    return strlen($string) > $length
        ? sprintf(
            "%s...%s",
            substr($string, 0, intval(ceil(($length - 3) / 2))),
            substr($string, intval(($length - 3) / -2))
        )
        : $string;
}
function is_number(string $string) {
    return !!preg_match('/^[+-]?[\d.]+(e[+-]?\d+)?$/', trim($string));
}

$personValues = [];
$attendanceValues = [];
$personRecords = XlsxReader::iterateRecords(__DIR__ . '/../../files/2023/Studierende Muwi WS 1927 28 Personen_MWA_5.10._ (4).xlsx');
$attendanceRecords = XlsxReader::iterateRecords(__DIR__ . '/../../files/2023/Studierende Muwi WS 1927 28 Vorlesungen_MWA_27.02.22 (1).xlsx');
foreach ($personRecords as $record) {
    foreach ($record as $k => $v) {
        if ($k === '::marked') continue;
        if (empty($v)) continue;
        if (!isset($personValues[$k]) || !in_array($v, $personValues[$k])) {
            $personValues[$k][] = $v;
        }
    }
}
foreach ($attendanceRecords as $record) {
    foreach ($record as $k => $v) {
        if ($k === '::marked') continue;
        if (empty($v)) continue;
        if (!isset($attendanceValues[$k]) || !in_array($v, $attendanceValues[$k])) {
            $attendanceValues[$k][] = $v;
        }
    }
}
foreach ([$personValues, $attendanceValues] as $i => $values) {
    foreach ($values as $k => $v) {
        sort($v);
        if ($k === '::marked') continue;
        file_put_contents(
            sprintf('%s/data/%s-%s.txt', __DIR__, $i ? 'attendance' : 'person', $k),
            implode(PHP_EOL, $v)
        );
        if (count($v) < 20) {
            printf(
                "%20s: %s",
                $k,
                implode(', ', array_map(fn($i) => json_encode(shorten($i, 20)), $v))
            );
        } elseif (count(array_filter(array_map('rekdagyothub\is_number', $v))) === count($v)) {
            printf("%20s: %d different numbers between %d and %d, e. g. %s", $k, count($v), min(...$v), max(...$v), json_encode($v[0]));
        }
        else {
            printf("%20s: %d different strings, max. %d long, e. g. %s", $k, count($v), max(...array_map('strlen', $v)), json_encode($v[0]));
        }
        echo PHP_EOL;
    }
    echo PHP_EOL, PHP_EOL;
}
