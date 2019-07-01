<?php declare(strict_types=1);
namespace rekdagyothub;

class PersonalTraitExtractor
{
    /** @var int */
    private $currentId;
    /** @var ImmutablePdoStatement */
    private $insertTimeStatement;
    /** @var ImmutablePdoStatement */
    private $insertValueStatement;

    public function __construct(ImmutablePdo $pdo, string $personalTraitName, array $valueColumns)
    {
        $statement = $pdo->query(sprintf('select max(`id`) as `id` from `student_%s_value`', $personalTraitName));
        foreach ($statement as $result) {
            $this->currentId = intval($result->id);
        }
        $this->insertValueStatement = $pdo->prepare(sprintf(
            "insert into `student_%s_value` (`id`, `person_id`, `%s`) values (%s)",
            $personalTraitName,
            implode('`, `', $valueColumns),
            implode(', ', array_fill(0, count($valueColumns) + 2, '?'))
        ));
        $this->insertTimeStatement = $pdo->prepare(
            sprintf(
                "insert into `student_%s_time` (`value_id`, `time`, `year_min`, `year_max`) values (?, ?, ?, ?)",
                $personalTraitName
            )
        );
    }

    public function extract($personId, $semester, $yearMin, $yearMax, ...$valueColumns): void
    {
        if (array_filter($valueColumns)) {
            $valueId = $this->nextId();
            $this->insertValueStatement->execute(array_merge([$valueId, $personId], $valueColumns));
            $this->insertTimeStatement->execute([$valueId, $semester, $yearMin, $yearMax]);
        }
    }

    protected function nextId(): int
    {
        return ++$this->currentId;
    }
}
