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
        printf("Loading input file %s\n", $fileInfo->getFilename());
        loadSqlTableFromCsvIterator($file->getBasename('.tsv'), $file, $pdo, 1000);
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
     * @param int $insertChunkSize
     */
    function loadSqlTableFromCsvIterator($rawTableName, Iterator $csvIterator, PDO $pdo, $insertChunkSize)
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
        $fieldCount = count($fields);
        $insert = $pdo->prepare(createSqlInsertStatement($tableName, $fieldCount, $insertChunkSize));
        $rowIndex = 0;
        $values = array();
        $currentRowCount = 0;
        foreach ($csvIterator as $rowIndex => $record) {
            if ($rowIndex > 0) {
                $values[] = $rowIndex; // add _id
                foreach ($record as $colIndex => $value) {
                    $value = trim($value);
                    $values[] = $value === '' || $value === 'NULL' ? null : $value;
                }
                $currentRowCount++;
                if ($rowIndex % $insertChunkSize == 0) {
                    $insert->execute($values);
                    $values = array();
                    $currentRowCount = 0;
                    printf("    Number of records processed: %d\n", $rowIndex);
                }
            }
        }
        if ($currentRowCount) {
            $pdo->prepare(createSqlInsertStatement($tableName, $fieldCount, $currentRowCount))->execute($values);
            printf("    Number of records processed: %d\n", $rowIndex);
        }
    }
