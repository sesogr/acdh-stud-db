<?php
require_once __DIR__ . '/../../web/src/credentials.php';
require_once __DIR__ . '/../../web/src/UnicodeString.php';
$pdo = openDbConnection('mysql:host=127.0.0.1;port=13006;dbname=rksd;charset=utf8', 'rksd', 'nJkyj2pOsfUi');
$studentRecords = iterateTsvFile(__DIR__ . '/../../files/final_student_person_Jus_1897_1927_mit_ID-1.color-columns.tsv');
$lectureRecords = iterateTsvFile(__DIR__ . '/../../files/final_student_lecture_Jus_1897_1927_mit_ID-1.tsv');
foreach ($studentRecords as $index => $record) {
    if ($index > 0) {
        processPersonRecord($record, $index, $pdo);
    }
}
// foreach ($lectureRecords as $index => $record) {
//     if ($index > 0) {
//         processLectureRecord($record, $index, $pdo);
//     }
// }
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

function guardExistingPersonRecords($id, $semester, PDO $pdo)
{
    $tables = explode(
        ' ',
        'biography birth_date birth_place ethnicity father gender given_names graduation guardian language last_name last_school literature nationality religion remarks studying_address'
    );
    $errors = array();
    foreach ($tables as $table) {
        $statement = $pdo->prepare(
            sprintf(
                <<<'EOD'
select *
from student_%s_time t
    join student_%1$s_value v on t.value_id = v.id
where v.person_id = ? and time = ?
EOD
                ,
                $table
            )
        );
        $statement->execute(array($id, $semester));
        foreach ($statement as $row) {
            $errors[] = $table;
        }
    }
    throw new RuntimeException("Same or similar records exist in the following tables: " . implode(', ', $errors));
    // print_r(array($lastName, $givenNames, decomposeUnicode($lastName), decomposeUnicode($givenNames)));
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
    $pdo = new PDO(
        $dsn,
        $username,
        $passwd,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        )
    );
    $pdo->exec('SET NAMES utf8');
    return $pdo;
}

function processLectureRecord(array $record, $index, PDO $pdo)
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

function processPersonRecord(array $record, $index, PDO $pdo)
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
    guardExistingPersonRecords($id, $semester, $pdo);
}
