<?php declare(strict_types=1);
namespace rekdagyothub;

use RuntimeException;

class PersonRecordProcessor extends RecordProcessor
{
    /** @var ImmutablePdoStatement */
    private $insertBioTimeStatement;
    /** @var ImmutablePdoStatement */
    private $insertBioValueStatement;

    public function __construct(ImmutablePdo $pdo)
    {
        parent::__construct($pdo);
        $this->insertBioValueStatement = $this->pdo->prepare(
            'insert into student_biography_value '
            . '(id, person_id, biography, is_from_supplemental_data_source) values (?, ?, ?, ?)'
        );
        $this->insertBioTimeStatement = $this->pdo->prepare(
            'insert into student_biography_time '
            . '(value_id, time, year_min, year_max) values (?, ?, ?, ?)'
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
            $bioValueId = $this->pdo->nextAutoId('student_biography_value', 'id');
            $this->insertBioValueStatement->execute([$bioValueId, $id, $biography, $isBiographyFromSupplementalSources]);
            $this->insertBioTimeStatement->execute([$bioValueId, $semester, $yearMin, $yearMax]);
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