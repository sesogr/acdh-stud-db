<?php declare(strict_types=1);
namespace rekdagyothub;

use PDOStatement;

/**
 * Accompanies ImmutablePdo in generating an SQL file from all those statements that manipulate date while still
 * returning rows for non-manipulation SELECT and SHOW statements.
 */
class ImmutablePdoStatement extends PDOStatement
{
    /** @var ImmutablePdo */
    private $pdo;
    /** @var string */
    private $statement;

    public function __construct(ImmutablePdo $pdo, string $statement)
    {
        $this->pdo = $pdo;
        $this->statement = $statement;
    }

    public function execute($input_parameters = null)
    {
        list($strings, $statement) = $this->extractStringsFromStatement($this->statement);
        foreach ($input_parameters as $key => $value) {
            $statement = preg_replace(
                is_numeric($key) ? '<\\?>' : '<\\Q' . $key . '\\E>',
                "\0",
                $statement,
                1
            );
            $strings[] = $this->pdo->quote(strval($value));
        }
        return $this->pdo->exec($this->recombineStringsIntoStatement($strings, $statement));
    }

    private function extractStringsFromStatement(string $statement): array
    {
        $tokens = preg_split("<(`[^`]+`|'[^']*')>", $statement, -1, PREG_SPLIT_DELIM_CAPTURE);
        return [
            array_filter($tokens, [$this, 'isOdd'], ARRAY_FILTER_USE_KEY),
            implode("\0", array_filter($tokens, [$this, 'isEven'], ARRAY_FILTER_USE_KEY)),
        ];
    }

    private function isEven(int $value): bool
    {
        return $value % 2 === 0;
    }

    private function isOdd(int $value): bool
    {
        return $value % 2 > 0;
    }

    private function recombineStringsIntoStatement(array $strings, string $statement): string
    {
        return preg_replace(array_fill(0, count($strings), "<\\0>"), $strings, $statement, 1);
    }
}