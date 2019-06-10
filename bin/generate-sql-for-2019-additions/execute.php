<?php declare(strict_types=1);
use rekdagyothub\ImmutablePdo;
use rekdagyothub\LectureRecordProcessor;
use rekdagyothub\PersonRecordProcessor;
use rekdagyothub\TsvReader;

require_once __DIR__ . '/../../lib/FacultyMap.php';
require_once __DIR__ . '/../../lib/ImmutablePdo.php';
require_once __DIR__ . '/../../lib/ImmutablePdoStatement.php';
require_once __DIR__ . '/../../lib/TsvReader.php';
require_once __DIR__ . '/../../lib/PersonalTraitExtractor.php';
require_once __DIR__ . '/../../lib/RecordProcessor.php';
require_once __DIR__ . '/../../lib/LectureRecordProcessor.php';
require_once __DIR__ . '/../../lib/PersonRecordProcessor.php';
require_once __DIR__ . '/../../web/src/credentials.php';
require_once __DIR__ . '/../../web/src/UnicodeString.php';
$sqlFile = __DIR__ . '/output.sql';
$dsn = 'mysql:host=127.0.0.1;port=13006;dbname=rksd;charset=utf8';
$dbUsername = 'rksd';
$dbPassword = 'nJkyj2pOsfUi';
$pdo = new ImmutablePdo($dsn, $dbUsername, $dbPassword, $sqlFile);
$personProcessor = new PersonRecordProcessor($pdo);
$lectureProcessor = new LectureRecordProcessor($pdo, $personProcessor);
$studentRecords = new TsvReader(__DIR__ . '/../../files/final_student_person_Jus_1897_1927_mit_ID-1.color-columns.tsv');
$lectureRecords = new TsvReader(__DIR__ . '/../../files/final_student_lecture_Jus_1897_1927_mit_ID-1.tsv');
foreach ($studentRecords as $index => $record) {
    if ($index > 0) {
        $personProcessor->processRecord($record, $index);
    }
}
foreach ($lectureRecords as $index => $record) {
    if ($index > 0) {
        $lectureProcessor->processRecord($record, $index);
    }
}
echo file_get_contents($sqlFile);
