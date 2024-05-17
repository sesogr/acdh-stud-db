<?php declare(strict_types=1);
use rekdagyothub\ImmutablePdo;
use rekdagyothub\LectureRecordProcessor;
use rekdagyothub\PersonRecordProcessor;
use rekdagyothub\XlsxReader;

include_once __DIR__ . '/../../vendor/autoload.php';
include_once __DIR__ . '/util.php';
require_once __DIR__ . '/../../web/src/credentials.php';
require_once __DIR__ . '/../../web/src/UnicodeString.php';
$sqlFile = __DIR__ . '/output.sql';
$dsn = 'mysql:host=127.0.0.1;port=13006;dbname=rksd;charset=utf8';
$pdo = new ImmutablePdo('mysql:host=127.0.0.1;port=13006;dbname=rksd;charset=utf8', MARIA_USER, MARIA_PASS, $sqlFile);
$personProcessor = new PersonRecordProcessor($pdo);
$lectureProcessor = new LectureRecordProcessor($pdo, $personProcessor);
$personRecords = XlsxReader::iterateRecords(__DIR__ . '/../../files/2023/Studierende Muwi WS 1927 28 Personen_MWA_5.10._ (4).xlsx');
$attendanceRecords = XlsxReader::iterateRecords(__DIR__ . '/../../files/2023/Studierende Muwi WS 1927 28 Vorlesungen_MWA_27.02.22 (1).xlsx');
foreach ($personRecords as $record) {
    [$yearMin, $yearMax] = detectYearMinMax($record['semester']);
    [$dobMin, $dobMax] = detectDOBMinMax(strval($record['geb']), in_array('geb', $record['::marked']), +$yearMin);
    if (!$record['X-serial-number']) continue;
    $personProcessor->processRecord(
        $record['X-serial-number'],
        false,
        $record['semester'],
        null,
        $yearMin,
        $yearMax,
        $record['name'],
        $record['geb'],
        false,
        $dobMin,
        $dobMax,
        $record['Staatsbürgerschaft'],
        $record['geb-ort'],
        $record['geb-land'],
        null,
        $record['mspr'],
        $record['rel'],
        $record['Wohnadr. Stud.'],
        $record['geschl'],
        $record['vater'],
        $record['vormund'],
        $record['Schule zuletzt'],
        null,
        false,
        null,
        false,
        $record['Literaturhinweise'],
        $record['Anmerkung'],
        $record['Volkszugehörigkeit'],
        in_array('geb', $record['::marked']),
        in_array('geb-ort', $record['::marked']),
        in_array('vater', $record['::marked']),
        in_array('geschl', $record['::marked']),
        in_array('vormund', $record['::marked']),
        in_array('Schule zuletzt', $record['::marked']),
        in_array('name', $record['::marked']),
        in_array('Anmerkung', $record['::marked']),
    );
}
foreach ($attendanceRecords as $record) {
    if (!$record['X-person-number']) continue;
    $lectureProcessor->processRecord(
        $record['X-person-number'],
        null,
        $record['X-semester'],
        $record['X-lecturer'],
        $record['X-class-extra'],
        $record['Anmerkungen'],
    );
}
echo file_get_contents($sqlFile);
