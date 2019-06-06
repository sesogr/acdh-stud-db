<?php declare(strict_types=1);
use rekdagyothub\ImmutablePdo;
use rekdagyothub\TsvReader;

require_once __DIR__ . '/../../lib/ImmutablePdo.php';
require_once __DIR__ . '/../../lib/ImmutablePdoStatement.php';
require_once __DIR__ . '/../../lib/TsvReader.php';
require_once __DIR__ . '/../../web/src/credentials.php';
require_once __DIR__ . '/../../web/src/UnicodeString.php';
$sqlFile = __DIR__ . '/output.sql';
$dsn = 'mysql:host=127.0.0.1;port=13006;dbname=rksd;charset=utf8';
$dbUsername = 'rksd';
$dbPassword = 'nJkyj2pOsfUi';
$pdo = openDbConnection($dsn, $dbUsername, $dbPassword, $sqlFile);
$studentRecords = new TsvReader(__DIR__ . '/../../files/final_student_person_Jus_1897_1927_mit_ID-1.color-columns.tsv');
$lectureRecords = new TsvReader(__DIR__ . '/../../files/final_student_lecture_Jus_1897_1927_mit_ID-1.tsv');
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
                str_replace(['(', ')', '[', ']', '{', '}', '<', '>'], '', $input)
            )
        )
    );
    return $unicodeString
        ->decompose(true)
        ->filter(null, [UnicodeString::LETTER, UnicodeString::SEPARATOR_SPACE])
        ->toLowerCase()
        ->saveUtf8String();
}

function guardPersonRecordIsNew($id, $semester, PDO $pdo)
{
    $tables = explode(
        ' ',
        'biography birth_date birth_place ethnicity father gender given_names graduation guardian language last_name last_school literature nationality religion remarks studying_address'
    );
    $errors = [];
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
        $statement->execute([$id, $semester]);
        foreach ($statement as $row) {
            $errors[] = $table;
        }
    }
    if ($errors) {
        throw new RuntimeException("Same or similar records exist in the following tables: " . implode(', ', $errors));
    }
}

function openDbConnection($dsn, $username, $passwd, $sqlFile)
{
    $pdo = new ImmutablePdo($dsn, $username, $passwd, $sqlFile);
    $pdo->exec('SET NAMES utf8mb4');
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
    print_r([$dozent, decomposeUnicode($dozent)]);
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
    guardPersonRecordIsNew($id, $semester, $pdo);
    $biography = trim($angabenZurBiografie . '; ' . $hinweiseZurBiografie, '; ') ?: null;
    if ($biography) {
        $pdo
            ->prepare('insert into student_biography_value (person_id, biography, is_from_supplemental_data_source) values (?, ?, ?)')
            ->execute([$id, $biography, $isBiographyFromSupplementalSources]);
    }
}
