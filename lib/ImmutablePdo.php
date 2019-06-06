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
    }

    public function prepare($statement, $driver_options = null)
    {
        if ($this->isReadonlyStatement($statement)) {
            return parent::prepare($statement);
        } else {
            file_put_contents($this->sqlFile, $this->terminateStatements($statement), FILE_APPEND);
            return new ImmutablePdoStatement($this, $statement);
        }
    }

    public function beginTransaction()
    {
        throw new RuntimeException('Transactions are not supported by this instance');
    }

    public function commit()
    {
        throw new RuntimeException('Transactions are not supported by this instance');
    }

    public function rollBack()
    {
        throw new RuntimeException('Transactions are not supported by this instance');
    }

    public function inTransaction()
    {
        return false;
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

    public function query($statement, $mode = PDO::ATTR_DEFAULT_FETCH_MODE, $arg3 = null, array $ctorargs = [])
    {
        if ($this->isReadonlyStatement($statement)) {
            return parent::query($statement, $mode, $arg3, $ctorargs);
        } else {
            file_put_contents($this->sqlFile, $this->terminateStatements($statement), FILE_APPEND);
            return false;
        }
    }

    private function isReadonlyStatement(string $statement): bool
    {
        return boolval(preg_match('<^(select|show)\\s+>i', $statement));
    }

    private function terminateStatements(string $statement): string
    {
        return preg_replace('<\\s*;\\s*$>m', '', $statement) . ";\n";
    }
}
