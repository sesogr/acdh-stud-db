<?php
    require_once __DIR__ . '/../../web/src/credentials.php';
    $pdo = new PDO('mysql:host=127.0.0.1;port=13006;dbname=rksd;charset=utf8', 'rksd', 'nJkyj2pOsfUi', array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ));
    $pdo->exec('SET NAMES utf8');
    $iterator = new DirectoryIterator('../../files/');
    $iterator = new CallbackFilterIterator($iterator, function (SplFileInfo $item, $key, Iterator $iterator) {
        return $item->isFile() && $item->isReadable() && $item->getExtension() === 'tsv';
    });
    /** @var SplFileInfo $fileInfo */

    foreach ($iterator as $fileInfo) {
        $file = $fileInfo->openFile('rb');
        $file->setCsvControl("\t", "\0", "\0");
        $file->setFlags(
            SplFileObject::DROP_NEW_LINE
            | SplFileObject::READ_AHEAD
            | SplFileObject::READ_CSV
            | SplFileObject::SKIP_EMPTY
        );
        loadSqlTableFromCsvIterator($file->getBasename('.tsv'), $file, $pdo);
    }
?>
<?php
    /**
     * @param string $tableName
     * @param string[] $fieldNames
     * @param int[] $fieldLengthsInBytes
     * @return string
     */
    function createSqlCreateTableStatement($tableName, array $fieldNames, array $fieldLengthsInBytes)
    {
        return sprintf(
            "CREATE TABLE `%s` (\n%s%s%s) ENGINE InnoDB DEFAULT CHARSET utf8",
            $tableName,
            "\t`_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,\n",
            implode(
                '',
                array_map(
                    function ($field, $length) {
                        return sprintf("\t`%s` VARCHAR(%d) DEFAULT NULL,\n", $field, $length);
                    },
                    $fieldNames,
                    $fieldLengthsInBytes
                )
            ),
            "\tPRIMARY KEY (`_id`)\n"
        );
    }

    /**
     * @param string $tableName
     * @param int $fieldCount
     * @return string
     */
    function createSqlInsertStatement($tableName, $fieldCount, $rowCount = 1)
    {
        $valuePlaceholders = sprintf('(?%s)', str_repeat(', ?', $fieldCount));
        return sprintf(
            'INSERT INTO `%s` VALUES %s',
            $tableName,
            implode(', ', array_fill(0, $rowCount, $valuePlaceholders))
        );
    }

    /**
     * @param string $fieldName
     * @param int $colIndex
     * @return string
     */
    function createSqlIdentifier($fieldName, $colIndex = null)
    {
        $umlautsReplaced = str_replace(
            array('Ä', 'Ö', 'Ü', 'ä', 'ö', 'ü', 'ß'),
            array('ae', 'oe', 'ue', 'ae', 'oe', 'ue', 'ss'),
            $fieldName
        );
        $fallback = $colIndex === null ? '' : '_field_' . $colIndex;

        return trim(preg_replace('<[^0-9a-z]+>', '_', strtolower($umlautsReplaced)), '_') ?: $fallback;
    }

    /**
     * @param string $rawTableName
     * @param Iterator $csvIterator
     * @param PDO $pdo
     */
    function loadSqlTableFromCsvIterator($rawTableName, Iterator $csvIterator, PDO $pdo)
    {
        $fields = array();
        $lengths = array();
        foreach ($csvIterator as $rowIndex => $record) {
            if ($rowIndex === 0) {
                foreach ($record as $colIndex => $fieldName) {
                    $fields[] = createSqlIdentifier($fieldName, $colIndex);
                    $lengths[] = 0;
                }
            } else {
                foreach ($record as $colIndex => $value) {
                    $lengths[$colIndex] = max(strlen($value), $lengths[$colIndex]);
                }
            }
        }
        $tableName = createSqlIdentifier($rawTableName);
        $pdo->exec(sprintf('DROP TABLE IF EXISTS `%s`', $tableName));
        $pdo->exec(createSqlCreateTableStatement($tableName, $fields, $lengths));
        foreach ($csvIterator as $rowIndex => $record) {
            if ($rowIndex > 0) {
                $values = array($rowIndex);
                foreach ($record as $colIndex => $value) {
                    $value = trim($value);
                    $values[] = $value === '' || $value === 'NULL' ? 'NULL' : $pdo->quote($value);
                }
                $pdo->exec(sprintf('INSERT INTO `%s` VALUES (%s)', $tableName, implode(', ', $values)));
            }
        }
    }
