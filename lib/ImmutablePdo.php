<?php declare(strict_types=1);
namespace rekdagyothub;

use PDO;
use RuntimeException;

/**
 * Generates an SQL file from all those statements that manipulate date while still returning rows for non-manipulation
 * SELECT and SHOW statements. It it configured to throw exceptions on error and return stdClass objects for fetched
 * rows by default (PDO::ERRMODE_EXCEPTION and PDO::FETCH_OBJ).
 */
class ImmutablePdo extends PDO
{
    private $autoIds = [];
    private $sqlFile;

    public function __construct($dsn, $username, $passwd, $sqlFile)
    {
        parent::__construct(
            $dsn,
            $username,
            $passwd,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            ]
        );
        $this->sqlFile = $sqlFile;
        file_put_contents($sqlFile, <<<'EOF'
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

EOF
);
    }
    public function __destruct()
    {
        file_put_contents($this->sqlFile, <<<'EOF'
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;

EOF
            ,
            FILE_APPEND
        );
    }

    public function beginTransaction()
    {
        throw new RuntimeException('Transactions are not supported by this instance');
    }

    public function commit()
    {
        throw new RuntimeException('Transactions are not supported by this instance');
    }

    public function exec($statement)
    {
        if ($this->isReadonlyStatement($statement)) {
            return parent::exec($statement);
        } else {
            file_put_contents($this->sqlFile, $this->terminateStatements($statement), FILE_APPEND);
            return 0;
        }
    }

    public function inTransaction()
    {
        return false;
    }

    public function nextAutoId(string $tableName, string $columnName = null): int
    {
        if (!isset($this->autoIds[$tableName])) {
            if (!$columnName) {
                $statement = $this->query(sprintf("show indexes from `%s` where key_name = 'primary'", $tableName));
                foreach ($statement as $result) {
                    $columnName = $result->Column_name;
                }
            }
            $statement = $this->query(sprintf('select max(`%s`) as `id` from `%s`', $columnName, $tableName));
            foreach ($statement as $result) {
                $this->autoIds[$tableName] = $result->id;
            }
        }
        return ++$this->autoIds[$tableName];
    }

    public function prepare($statement, $driver_options = null)
    {
        if ($this->isReadonlyStatement($statement)) {
            return parent::prepare($statement);
        } else {
            return new ImmutablePdoStatement($this, $statement);
        }
    }

    public function query($statement, ...$args)
    {
        if ($this->isReadonlyStatement($statement)) {
            return parent::query($statement, ...$args);
        } else {
            file_put_contents($this->sqlFile, $this->terminateStatements($statement), FILE_APPEND);
            return false;
        }
    }

    public function rollBack()
    {
        throw new RuntimeException('Transactions are not supported by this instance');
    }

    private function isReadonlyStatement(string $statement): bool
    {
        return boolval(preg_match('<^(select|show|set\\s+names)\\s+>i', $statement));
    }

    private function terminateStatements(string $statement): string
    {
        return preg_replace('<\\s*;\\s*$>m', '', $statement) . ";\n";
    }
}
