<?php
require_once __DIR__ . '/../../web/src/credentials.php';
require_once __DIR__ . '/../../web/src/UnicodeString.php';
$pdo = openDbConnection('mysql:host=127.0.0.1;port=13006;dbname=rksd;charset=utf8', 'rksd', 'nJkyj2pOsfUi');
$studentRecords = iterateTsvFile(__DIR__ . '/../../files/final_student_person_Jus_1897_1927_mit_ID-1.color-columns.tsv');
$lectureRecords = iterateTsvFile(__DIR__ . '/../../files/final_student_lecture_Jus_1897_1927_mit_ID-1.tsv');
foreach ($studentRecords as $index => $record) {
    if ($index > 0) {
        processPersonRecord($record, $index);
    }
}
foreach ($lectureRecords as $index => $record) {
    if ($index > 0) {
        processLectureRecord($record, $index);
    }
}
//
function decomposeUnicode($input)
{
    $unicodeString = new UnicodeString();
    $unicodeString->loadUtf8String(
        trim(
            preg_replace(
                '/[^a-z\\x80-\\xff]+/i',
                ' ',
                str_replace(array('(', ')', '[', ']', '{', '}', '<', '>'), '', $input)
            )
        )
    );
    return $unicodeString
        ->decompose(true)
        ->filter(null, array(UnicodeString::LETTER, UnicodeString::SEPARATOR_SPACE))
        ->toLowerCase()
        ->saveUtf8String();
}
function iterateTsvFile($fileName)
{
    $csvReader = new SplFileObject($fileName);
    $csvReader->setCsvControl("\t", '"', '"');
    $csvReader->setFlags(
        SplFileObject::DROP_NEW_LINE
        | SplFileObject::READ_AHEAD
        | SplFileObject::READ_CSV
        | SplFileObject::SKIP_EMPTY
    );
    return $csvReader;
}
function openDbConnection($dsn, $username, $passwd)
{
    $pdo = new PDO($dsn, $username, $passwd, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $pdo->exec('SET NAMES utf8');
    return $pdo;
}
function processLectureRecord(array $record, $index)
{
    list(
        $merged,
        $id,
        $wsSs,
        $semester,
        $dozent,
        $vorlesung,
        $anmerkung
        ) = $record;
    print_r(array($dozent, decomposeUnicode($dozent)));
}
function processPersonRecord(array $record, $index)
{
    list(
        $id,
        $isExistingId,
        $lfdNr,
        $semester,
        $fakultaet,
        $yearMin,
        $yearMax,
        $name,
        $jahreAlt,
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
        $volkszugehoerigkeit
        ) = $record;
    if (preg_match('<^([a-z\\x80-\\xff]+)\\s*(\\[[^\\]]+\\])\\s+(\\S+)$>i', $name, $matches)) {
        $lastName = $matches[1] . ' ' . $matches[2];
        $givenNames = $matches[3];
    } else {
        list($lastName, $givenNames) = explode(' ', $name, 2);
    }
    print_r(array($lastName, $givenNames, decomposeUnicode($lastName), decomposeUnicode($givenNames)));
}
