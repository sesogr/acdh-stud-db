<?php declare(strict_types=1);
namespace rekdagyothub;

class LectureRecordProcessor extends RecordProcessor
{
    /** @var FacultyMap */
    private $facultyMap;
    /** @var bool|\PDOStatement|ImmutablePdoStatement */
    private $insertStatement;

    public function __construct(ImmutablePdo $pdo, FacultyMap $facultyMap)
    {
        parent::__construct($pdo);
        $this->facultyMap = $facultyMap;
        $this->insertStatement = $pdo->prepare(
            'insert into student_attendance '
            . '(person_id, semester_abs, semester_rel, faculty, lecturer, class, remarks, ascii_lecturer) '
            . 'values (?, ?, ?, ?, ?, ?, ?, ?)'
        );
    }

    public function processRecord($merged = null,
                                  $wsSs = null,
                                  $semester = null,
                                  $dozent = null,
                                  $vorlesung = null,
                                  $anmerkung = null): void
    {
        $faculty = $this->facultyMap->getFacultyForPersonId(intval($merged));
        $this->insertStatement->execute(
            [$merged, $wsSs, $semester, $faculty, $dozent, $vorlesung, $anmerkung, $this->decomposeUnicode($dozent)]
        );
    }
}
