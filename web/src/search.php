<?php
    require_once __DIR__ . '/credentials.php';
    $pdo = new PDO(MARIA_DSN, MARIA_USER, MARIA_PASS, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $params['name'] = isset($_POST['ce133f40']) ? reset($_POST['ce133f40']) : '*';
    $params['country'] = isset($_POST['e95c7283']) ? reset($_POST['e95c7283']) : '*';
    $params['language'] = isset($_POST['8cd799d0']) ? reset($_POST['8cd799d0']) : '*';
    $params['religion'] = isset($_POST['10b19606']) ? reset($_POST['10b19606']) : '*';
    $params['begin'] = isset($_POST['1b3abf22']) ? reset($_POST['1b3abf22']) : 0;
    $params['end'] = isset($_POST['5807411e']) ? reset($_POST['5807411e']) : 9999;
    $params['includeNull'] = isset($_POST['d0319d7c']) && reset($_POST['0ced7cdc']);
    $params['semester'] = isset($_POST['0ced7cdc']) ? reset($_POST['0ced7cdc']) : '*';
    $params['lecturer'] = isset($_POST['f04509d6']) ? reset($_POST['f04509d6']) : '*';
    $sort = isset($_POST['2068e07a']) ? reset($_POST['2068e07a']) : 'name';
    $order = isset($_POST['e938f5ac']) && reset($_POST['e938f5ac']) === 'desc' ? 'desc' : 'asc';
    $sort = $sort && in_array($sort, array('geb', 'geb_land', 'rel', 'mspr', 'geschl', 'name')) ? $sort : 'name';
    $query = <<<'EOD'
SELECT `s`.*
FROM `student_person` `s`
LEFT JOIN `student_vorlesung` `l` ON `l`.`student_id` = `s`.`id`
WHERE (:name = '*' OR :name = ifnull(`s`.`name`, ''))
AND (:country = '*' OR :country = ifnull(`s`.`geb_land`, ''))
AND (:language = '*' OR :language = ifnull(`s`.`mspr`, ''))
AND (:religion = '*' OR :religion = ifnull(`s`.`rel`, ''))
AND (:lecturer = '*' OR :lecturer = ifnull(`l`.`lecturer`, ''))
AND (:begin <= `l`.`semester_year_begin` OR :includeNull > 0 AND `l`.`semester_year_begin` IS NULL)
AND (:end >= `l`.`semester_year_end` OR :includeNull > 0 AND `l`.`semester_year_end` IS NULL)
GROUP BY `s`.`id`
ORDER BY `s`.`%s` %s
EOD;
    $listStudents = $pdo->prepare(sprintf($query, $sort, $order));
    $listStudents->setFetchMode(PDO::FETCH_ASSOC);
    $listStudents->execute($params);
    $rowCount = $listStudents->rowCount();
?>
<p>Ihre Suche lieferte <?php echo $rowCount ? $rowCount : 'keine' ?> Treffer.</p>
<?php if ($rowCount): ?>
    <pre><?php var_dump($params) ?></pre>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Geschlecht</th>
                <th>Geb.</th>
                <th>Geb.-Land</th>
                <th>Religion</th>
                <th>Zeit</th>
                <th>Detailansicht</th>
            </tr>
        </thead>
        <tbody>
            <?php /** @var array $student */ foreach ($listStudents as $student): ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['name'])?></td>
                    <td><?php echo htmlspecialchars($student['geschl'])?></td>
                    <td><?php echo htmlspecialchars($student['geb'])?></td>
                    <td><?php echo htmlspecialchars($student['geb_land'])?></td>
                    <td><?php echo htmlspecialchars($student['rel'])?></td>
                    <td><?php echo htmlspecialchars($student['zeit'])?></td>
                    <td><a href="?id=<?php echo htmlspecialchars($student['id'])?>">anzeigen</a></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
<?php endif ?>
