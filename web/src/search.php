<?php
    require_once __DIR__ . '/credentials.php';
    $pageSize = 100;
    $pageNo = 0;
    $pdo = new PDO(MARIA_DSN, MARIA_USER, MARIA_PASS, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $pdo->exec('SET NAMES utf8');
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
    $sort = $sort && in_array($sort, array('birth_date', 'birth_country_historic', 'birth_country_today', 'religion', 'language', 'gender')) ? $sort : "concat_ws(' ', last_name, given_names)";
    $query =
        /** @lang MySQL */
        <<<'EOD'
select sql_calc_found_rows *
from student_identity i
left JOIN student_last_name_value ln on ln.person_id = i.person_id
left JOIN student_given_names_value gn on gn.person_id = i.person_id
left JOIN student_birth_place_value bp on bp.person_id = i.person_id
left JOIN student_birth_date_value bd on bd.person_id = i.person_id
left JOIN student_gender_value g on g.person_id = i.person_id
left JOIN student_language_value l on l.person_id = i.person_id
left JOIN student_religion_value r on r.person_id = i.person_id
left JOIN student_attendance a on a.person_id = i.person_id
WHERE (:name = '*' OR :name = ln.last_name OR :name = concat_ws(' ', gn.given_names, ln.last_name) OR :name = concat_ws(', ', ln.last_name, gn.given_names))
AND (:country = '*' OR :country = ifnull(bp.birth_country_historic, '') OR :country = ifnull(bp.birth_country_today, ''))
AND (:language = '*' OR :language = ifnull(l.language, ''))
AND (:religion = '*' OR :religion = ifnull(r.religion, ''))
AND (:lecturer = '*' OR :lecturer = ifnull(a.lecturer, ''))
AND (:semester = '*' OR :semester = ifnull(a.semester_abs, ''))
AND (i.year_min between :begin and :end or i.year_max between :begin and :end or :includeNull and i.year_min is null)
GROUP BY i.person_id
ORDER BY %s %s
limit %d offset %d
EOD;
    $listStudents = $pdo->prepare(sprintf($query, $sort, $order, $pageSize, $pageNo * $pageSize));
    $listStudents->setFetchMode(PDO::FETCH_ASSOC);
    $listStudents->execute($params);
    $rowCount = $pdo->query(/** @lang MySQL */'select found_rows()')->fetchColumn(0) - 0;
    $pageCount = ceil($rowCount / $pageSize);
?>
<p>Ihre Suche lieferte <?php echo $rowCount ? $rowCount : 'keine' ?> Treffer.</p>
<?php if ($rowCount): ?>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Geschlecht</th>
                <th>Geb.</th>
                <th>Geb.-Land (hist.)</th>
                <th>Geb.-Land (heute)</th>
                <th>Religion</th>
                <th>Zeitraum</th>
                <th>Detailansicht</th>
            </tr>
        </thead>
        <tbody>
            <?php /** @var array $student */ foreach ($listStudents as $student): ?>
                <tr>
                    <td><?php echo htmlspecialchars(
                            implode(', ', array($student['last_name'], $student['given_names']))
                        ) ?></td>
                    <td><?php echo htmlspecialchars($student['gender']) ?></td>
                    <td><?php echo htmlspecialchars($student['birth_date']) ?></td>
                    <td><?php echo htmlspecialchars($student['birth_country_historic']) ?></td>
                    <td><?php echo htmlspecialchars($student['birth_country_today']) ?></td>
                    <td><?php echo htmlspecialchars($student['religion']) ?></td>
                    <td><?php echo htmlspecialchars(
                            $student['year_min'] === null ? ''
                                : ($student['year_min'] == $student['year_max'] ? $student['year_min']
                                : sprintf('%d..%d', $student['year_min'], $student['year_max']))
                        ) ?></td>
                    <td><a href="?id=<?php echo htmlspecialchars($student['person_id']) ?>">anzeigen</a></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
<?php endif ?>
