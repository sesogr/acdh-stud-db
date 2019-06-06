<?php declare(strict_types=1);
use rekdagyothub\ImmutablePdo;
use rekdagyothub\ImmutablePdoStatement;
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
$pdo = new ImmutablePdo($dsn, $dbUsername, $dbPassword, $sqlFile);
$personProcessor = new PersonRecordProcessor($pdo);
$lectureProcessor = new LectureRecordProcessor($pdo);
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

abstract class RecordProcessor
{
    /** @var ImmutablePdo */
    protected $pdo;

    public function __construct(ImmutablePdo $pdo)
    {
        $this->pdo = $pdo;
        $this->pdo->exec('SET NAMES utf8mb4');
    }

    abstract public function processRecord(array $record, $index);

    protected function decomposeUnicode($input)
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
}

class LectureRecordProcessor extends RecordProcessor
{
    public function processRecord(array $record, $index)
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
        print_r([$dozent, $this->decomposeUnicode($dozent)]);
    }
}

class PersonRecordProcessor extends RecordProcessor
{
    /** @var ImmutablePdoStatement */
    private $insertBioValueStatement;

    public function __construct(ImmutablePdo $pdo)
    {
        parent::__construct($pdo);
        $this->insertBioValueStatement = $this->pdo->prepare(
            'insert into student_biography_value (person_id, biography, is_from_supplemental_data_source) values (?, ?, ?)'
        );
    }

    public function processRecord(array $record, $index)
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
        $this->guardPersonRecordIsNew($id, $semester);
        $biography = trim($angabenZurBiografie . '; ' . $hinweiseZurBiografie, '; ') ?: null;
        if ($biography) {
            $this->insertBioValueStatement
                ->execute([$id, $biography, $isBiographyFromSupplementalSources]);
        }
    }

    protected function guardPersonRecordIsNew($id, $semester)
    {
        $tables = explode(
            ' ',
            'biography birth_date birth_place ethnicity father gender given_names graduation guardian language last_name last_school literature nationality religion remarks studying_address'
        );
        $errors = [];
        foreach ($tables as $table) {
            $statement = $this->pdo->prepare(
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
}