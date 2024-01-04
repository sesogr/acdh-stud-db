<?php declare(strict_types=1);
namespace rekdagyothub;

include_once __DIR__ . '/../../vendor/autoload.php';
$numberMin = INF;
$numberMax = 0;
$serNoMin = INF;
$serNoMax = 0;
$perNoMin = INF;
$perNoMax = 0;
$personRecords = iterator_to_array(XlsxReader::iterateRecords(__DIR__ . '/../../files/2023/Studierende Muwi WS 1927 28 Personen_MWA_5.10._ (4).xlsx'));
$attendanceRecords = iterator_to_array(XlsxReader::iterateRecords(__DIR__ . '/../../files/2023/Studierende Muwi WS 1927 28 Vorlesungen_MWA_27.02.22 (1).xlsx'));

shuffle($personRecords);
shuffle($attendanceRecords);
foreach ($personRecords as $v) {
    $needNewline = false;
    if (!empty($v['Nr.'])) {
        if ($v['Nr.'] < $numberMin || $v['Nr.'] > $numberMax) {
            printf("        Number min: %5s; max %5d; reading new value %5d | ", $numberMin, $numberMax, $v['Nr.']);
            $needNewline = true;
        }
        $numberMin = min($numberMin, $v['Nr.']);
        $numberMax = max($numberMax, $v['Nr.']);
    }
    if (!empty($v['X-serial-number'])) {
        if ($v['X-serial-number'] < $serNoMin || $v['X-serial-number'] > $serNoMax) {
            printf(" Serial number min: %5s; max %5d; reading new value %5d", $serNoMin, $serNoMax, $v['X-serial-number']);
            $needNewline = true;
        }
        $serNoMin = min($serNoMin, $v['X-serial-number']);
        $serNoMax = max($serNoMax, $v['X-serial-number']);
    }
    if ($needNewline) {
        echo PHP_EOL;
    }
}
printf("        Number min: %5s; max %5d                          | ", $numberMin, $numberMax);
printf(" Serial number min: %5s; max %5d%s%s", $serNoMin, $serNoMax, PHP_EOL, PHP_EOL);
foreach ($attendanceRecords as $k => $v) {
    if (!empty($v['X-person-number'])) {
        if ($v['X-person-number'] < $perNoMin || $v['X-person-number'] > $perNoMax) {
            printf(" Person number min: %5s; max %5d; reading new value %5d%s", $perNoMin, $perNoMax, $v['X-person-number'], PHP_EOL);
        }
        $perNoMin = min($perNoMin, $v['X-person-number']);
        $perNoMax = max($perNoMax, $v['X-person-number']);
    }
}
printf(" Person number min: %5s; max %5d%s", $perNoMin, $perNoMax, PHP_EOL);
