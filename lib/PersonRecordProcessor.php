<?php declare(strict_types=1);
namespace rekdagyothub;

use RuntimeException;

class PersonRecordProcessor extends RecordProcessor
{
    /** @var PersonalTraitExtractor */
    private $biographyExtractor;
    /** @var PersonalTraitExtractor */
    private $birthDateExtractor;
    /** @var PersonalTraitExtractor */
    private $birthPlaceExtractor;
    /** @var PersonalTraitExtractor */
    private $ethnicityExtractor;
    /** @var string[] */
    private $facultyMap = [];
    /** @var PersonalTraitExtractor */
    private $fatherExtractor;
    /** @var PersonalTraitExtractor */
    private $genderExtractor;
    /** @var PersonalTraitExtractor */
    private $givenNamesExtractor;
    /** @var PersonalTraitExtractor */
    private $graduationExtractor;
    /** @var PersonalTraitExtractor */
    private $guardianExtractor;
    /** @var ImmutablePdoStatement */
    private $insertIdentityStatement;
    /** @var PersonalTraitExtractor */
    private $languageExtractor;
    /** @var PersonalTraitExtractor */
    private $lastNameExtractor;
    /** @var PersonalTraitExtractor */
    private $lastSchoolExtractor;
    /** @var PersonalTraitExtractor */
    private $literatureExtractor;
    /** @var PersonalTraitExtractor */
    private $nationalityExtractor;
    /** @var PersonalTraitExtractor */
    private $religionExtractor;
    /** @var PersonalTraitExtractor */
    private $remarksExtractor;
    /** @var PersonalTraitExtractor */
    private $studyingAddressExtractor;

    public function __construct(ImmutablePdo $pdo)
    {
        parent::__construct($pdo);
        $this->insertIdentityStatement = $this->pdo->prepare(
            'insert into student_identity (person_id, year_min, year_max) values (?, ?, ?)'
        );
        $this->biographyExtractor = new PersonalTraitExtractor($this->pdo, 'biography', ['biography', 'is_from_supplemental_data_source']);
        $this->birthDateExtractor = new PersonalTraitExtractor($this->pdo, 'birth_date', ['birth_date', 'born_on_or_after', 'born_on_or_before', 'is_from_supplemental_data_source']);
        $this->birthPlaceExtractor = new PersonalTraitExtractor($this->pdo, 'birth_place', ['birth_place', 'birth_country_historic', 'birth_country_today']);
        $this->ethnicityExtractor = new PersonalTraitExtractor($this->pdo, 'ethnicity', ['ethnicity']);
        $this->fatherExtractor = new PersonalTraitExtractor($this->pdo, 'father', ['father']);
        $this->genderExtractor = new PersonalTraitExtractor($this->pdo, 'gender', ['gender']);
        $this->givenNamesExtractor = new PersonalTraitExtractor($this->pdo, 'given_names', ['given_names', 'ascii_given_names']);
        $this->graduationExtractor = new PersonalTraitExtractor($this->pdo, 'graduation', ['graduation']);
        $this->guardianExtractor = new PersonalTraitExtractor($this->pdo, 'guardian', ['guardian']);
        $this->languageExtractor = new PersonalTraitExtractor($this->pdo, 'language', ['language']);
        $this->lastNameExtractor = new PersonalTraitExtractor($this->pdo, 'last_name', ['last_name', 'ascii_last_name']);
        $this->lastSchoolExtractor = new PersonalTraitExtractor($this->pdo, 'last_school', ['last_school']);
        $this->literatureExtractor = new PersonalTraitExtractor($this->pdo, 'literature', ['literature']);
        $this->nationalityExtractor = new PersonalTraitExtractor($this->pdo, 'nationality', ['nationality']);
        $this->religionExtractor = new PersonalTraitExtractor($this->pdo, 'religion', ['religion']);
        $this->remarksExtractor = new PersonalTraitExtractor($this->pdo, 'remarks', ['remarks']);
        $this->studyingAddressExtractor = new PersonalTraitExtractor($this->pdo, 'studying_address', ['studying_address']);
    }

    public function getFacultyForPersonId(int $id): ?string
    {
        return $this->facultyMap[$id] ?? null;
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
        $this->guardPersonRecordIsNew($id, $semester);
        $this->facultyMap[$id] = $fakultaet;
        if (preg_match('<^([a-z\\x80-\\xff]+)\\s*(\\[[^\\]]+\\])\\s+(\\S+)$>i', $name, $matches)) {
            $lastName = $matches[1] . ' ' . $matches[2];
            $givenNames = $matches[3];
        } else {
            list($lastName, $givenNames) = explode(' ', $name, 2);
        }
        if (!$isExistingId) {
            $this->insertIdentityStatement->execute([$id, $yearMin, $yearMax]);
        }
        $this->biographyExtractor->extract(
            $id,
            $semester,
            $yearMin,
            $yearMax,
            trim($angabenZurBiografie . '; ' . $hinweiseZurBiografie, '; ') ?: null,
            $isBiographyFromSupplementalSources || $isCommentFromSupplementalSources
        );
        $this->birthDateExtractor->extract(
            $id,
            $semester,
            $yearMin,
            $yearMax,
            $gebDat,
            $bornMin,
            $bornMax,
            $isDobFromSupplementalSources
        );
        $this->birthPlaceExtractor->extract(
            $id,
            $semester,
            $yearMin,
            $yearMax,
            $geburtsort,
            $geburtslandHistorisch,
            $geburtslandHeute
        );
        $this->ethnicityExtractor->extract(
            $id,
            $semester,
            $yearMin,
            $yearMax,
            $volkszugehoerigkeit
        );
        $this->fatherExtractor->extract(
            $id,
            $semester,
            $yearMin,
            $yearMax,
            $nameStandUWohnortDVaters
        );
        $this->genderExtractor->extract(
            $id,
            $semester,
            $yearMin,
            $yearMax,
            $geschlecht
        );
        $this->givenNamesExtractor->extract(
            $id,
            $semester,
            $yearMin,
            $yearMax,
            $givenNames,
            $this->decomposeUnicode($givenNames)
        );
        $this->guardianExtractor->extract(
            $id,
            $semester,
            $yearMin,
            $yearMax,
            $vormund
        );
        $this->languageExtractor->extract(
            $id,
            $semester,
            $yearMin,
            $yearMax,
            $muttersprache
        );
        $this->lastNameExtractor->extract(
            $id,
            $semester,
            $yearMin,
            $yearMax,
            $lastName,
            $this->decomposeUnicode($lastName)
        );
        $this->lastSchoolExtractor->extract(
            $id,
            $semester,
            $yearMin,
            $yearMax,
            $zuletztBesuchteLehranstaltGrundlageFuerImmatriculation
        );
        $this->literatureExtractor->extract(
            $id,
            $semester,
            $yearMin,
            $yearMax,
            $literaturhinweise
        );
        $this->nationalityExtractor->extract(
            $id,
            $semester,
            $yearMin,
            $yearMax,
            $staatsbuergerschaft
        );
        $this->religionExtractor->extract(
            $id,
            $semester,
            $yearMin,
            $yearMax,
            $religion
        );
        $this->remarksExtractor->extract(
            $id,
            $semester,
            $yearMin,
            $yearMax,
            $anmerkung
        );
        $this->studyingAddressExtractor->extract(
            $id,
            $semester,
            $yearMin,
            $yearMax,
            $adresseStudent
        );
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