<?php declare(strict_types=1);
use rekdagyothub\ImmutablePdo;
use rekdagyothub\LectureRecordProcessor;
use rekdagyothub\PersonRecordProcessor;
use rekdagyothub\TsvReader;

include_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../web/src/credentials.php';
require_once __DIR__ . '/../../web/src/UnicodeString.php';
$sqlFile = __DIR__ . '/output.sql';
$pdo = new ImmutablePdo('mysql:host=127.0.0.1;port=13006;dbname=rksd;charset=utf8', MARIA_USER, MARIA_PASS, $sqlFile);
$personProcessor = new PersonRecordProcessor($pdo);
$lectureProcessor = new LectureRecordProcessor($pdo, $personProcessor);
$studentRecords = new TsvReader(__DIR__ . '/../../files/final_student_person_Jus_1897_1927_mit_ID-1.color-columns.tsv');
$lectureRecords = new TsvReader(__DIR__ . '/../../files/final_student_lecture_Jus_1897_1927_mit_ID-1.tsv');
foreach ($studentRecords as $index => [$id,
         $isExistingId,
    /*lfd Nr.*/,
         $semester,
         $fakultaet,
         $yearMin,
         $yearMax,
         $name,
    /*Jahre alt*/,
         $gebDat,
         $isDobFromSupplementalSources,
         $bornMin,
         $bornMax,
         $staatsbuergerschaft,
         $geburtsort,
         $geburtslandHistorisch,
         $geburtslandHeute,
         $muttersprache,
         $religion,
         $adresseStudent,
         $geschlecht,
         $nameStandUWohnortDVaters,
         $vormund,
         $zuletztBesuchteLehranstaltGrundlageFuerImmatriculation,
         $angabenZurBiografie,
         $isBiographyFromSupplementalSources,
         $hinweiseZurBiografie,
         $isCommentFromSupplementalSources,
         $literaturhinweise,
         $anmerkung,
         $volkszugehoerigkeit]) {
    if ($index > 0) {
        $personProcessor->processRecord($id,
            $isExistingId,
            $semester,
            $fakultaet,
            $yearMin,
            $yearMax,
            $name,
            $gebDat,
            $isDobFromSupplementalSources,
            $bornMin,
            $bornMax,
            $staatsbuergerschaft,
            $geburtsort,
            $geburtslandHistorisch,
            $geburtslandHeute,
            $muttersprache,
            $religion,
            $adresseStudent,
            $geschlecht,
            $nameStandUWohnortDVaters,
            $vormund,
            $zuletztBesuchteLehranstaltGrundlageFuerImmatriculation,
            $angabenZurBiografie,
            $isBiographyFromSupplementalSources,
            $hinweiseZurBiografie,
            $isCommentFromSupplementalSources,
            $literaturhinweise,
            $anmerkung,
            $volkszugehoerigkeit);
    }
}
foreach ($lectureRecords as $index => [$merged,
    /*ID*/,
         $wsSs,
         $semester,
         $dozent,
         $vorlesung,
         $anmerkung]) {
    if ($index > 0) {
        $lectureProcessor->processRecord($merged,
            $wsSs,
            $semester,
            $dozent,
            $vorlesung,
            $anmerkung);
    }
}
echo file_get_contents($sqlFile);
