<?php
    require_once __DIR__ . '/credentials.php';
    require_once __DIR__ . '/UnicodeString.php';
    $pageSize = 100;
    $pageNo = 0;
    $pdo = new PDO(MARIA_DSN, MARIA_USER, MARIA_PASS, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $pdo->exec('SET NAMES utf8');
    $params = array();
    if (!empty($_GET['token'])) {
        if ($compressed = base64_decode($_GET['token'], true)) {
            if ($serialization = gzuncompress($compressed)) {
                if ($data = unserialize($serialization)) {
                    list($pageNo, $pageSize, $params, $sort, $order) = $data;
                }
            }
        }
    }
    if (empty($params)) {
        $unicode = new UnicodeString();
        foreach (array('ce133f40', 'f04509d6') as $index) {
            if (isset($_POST[$index])) {
                $normalizedName = str_replace(array('(', ')', '[', ']', '{', '}', '<', '>'), '', reset($_POST[$index]));
                $normalizedName = preg_replace('/[^,a-z\\x80-\\xff]+/i', ' ', $normalizedName);
                $unicode->loadUtf8String(trim($normalizedName));
                $asciiName = $unicode
                    ->decompose(true)
                    ->filter(null, array(UnicodeString::LETTER, UnicodeString::SEPARATOR_SPACE, UnicodeString::PUNCTUATION_OTHER))
                    ->toLowerCase()
                    ->saveUtf8String();
                $_POST[$index] = array($asciiName);
            }
        }
        $params['name'] = isset($_POST['ce133f40']) ? reset($_POST['ce133f40']) : '*';
        $params['country'] = isset($_POST['e95c7283']) ? reset($_POST['e95c7283']) : '*';
        $params['language'] = isset($_POST['8cd799d0']) ? reset($_POST['8cd799d0']) : '*';
        $params['religion'] = isset($_POST['10b19606']) ? reset($_POST['10b19606']) : '*';
        $params['begin'] = isset($_POST['1b3abf22']) ? reset($_POST['1b3abf22']) : 0.0;
        $params['end'] = isset($_POST['5807411e']) ? reset($_POST['5807411e']) : 9999.5;
        $params['lecturer'] = isset($_POST['f04509d6']) ? reset($_POST['f04509d6']) : '*';
        $sort = isset($_POST['2068e07a']) ? reset($_POST['2068e07a']) : 'name';
        $order = isset($_POST['e938f5ac']) && reset($_POST['e938f5ac']) === 'desc' ? 'desc' : 'asc';
        $sort = $sort && in_array($sort, array('birth_date', 'birth_country_historic', 'birth_country_today', 'religion', 'language', 'gender')) ? $sort : "concat_ws(' ', ascii_last_name, ascii_given_names)";
    }
    $query =
        /** @lang MySQL */
        <<<'EOD'
select sql_calc_found_rows
    *
from student_identity i
left JOIN student_last_name_value ln on ln.person_id = i.person_id
left JOIN student_given_names_value gn on gn.person_id = i.person_id
left JOIN student_birth_place_value bp on bp.person_id = i.person_id
left JOIN v_most_precise_birth_date bd on bd.person_id = i.person_id
left JOIN student_gender_value g on g.person_id = i.person_id
left JOIN student_language_value l on l.person_id = i.person_id
left JOIN student_religion_value r on r.person_id = i.person_id
left JOIN student_attendance a on a.person_id = i.person_id
WHERE (:name = '*' OR :name = ln.ascii_last_name OR concat_ws(' ', gn.ascii_given_names, ln.ascii_last_name) like concat('%%', :name, '%%'))
AND (:country = '*' OR :country = ifnull(bp.birth_country_historic, '') OR :country = ifnull(bp.birth_country_today, ''))
AND (:language = '*' OR :language = ifnull(l.language, ''))
AND (:religion = '*' OR :religion = ifnull(r.religion, '') or r.religion like concat(:religion, '%%'))
AND (:lecturer = '*' OR :lecturer = ifnull(a.ascii_lecturer, '') or a.ascii_lecturer like concat(:lecturer, '%%'))
AND substr(a.semester_abs from 3 FOR 4) + if(a.semester_abs like 'W %%', 0.5, 0.0) BETWEEN :begin and :end
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
    <p>Seite
        <?php for ($i = 0; $i < $pageCount; $i++): ?>
            <?php if ($i === $pageNo): ?>
                <strong><?php echo $i + 1 ?></strong>
            <?php else: ?>
                <a href="?token=<?php
                    echo urlencode(base64_encode(gzcompress(serialize(array($i, $pageSize, $params, $sort, $order)))))
                ?>"><?php echo $i + 1 ?></a>
            <?php endif ?>
        <?php endfor ?>
    </p>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Geb.</th>
                <th>Geb.-Ort</th>
                <th>Geb.-Land (hist.)</th>
                <th>Geb.-Land (heute)</th>
                <th>Detailansicht</th>
            </tr>
        </thead>
        <tbody>
            <?php /** @var array $student */ foreach ($listStudents as $student): ?>
                <tr>
                    <td><?php echo htmlspecialchars(
                            implode(', ', array($student['last_name'], $student['given_names']))
                        ) ?></td>
                    <td<?php echo $student['is_from_supplemental_data_source'] ? ' title="Aus zusätzlichen Quellen ergänzt"' : ''
                        ?>><?php echo htmlspecialchars($student['birth_date'])
                        ?></td>
                    <td><?php echo htmlspecialchars($student['birth_place']) ?></td>
                    <td><?php echo htmlspecialchars($student['birth_country_historic']) ?></td>
                    <td><?php echo htmlspecialchars($student['birth_country_today']) ?></td>
                    <td><a href="?id=<?php echo htmlspecialchars($student['person_id']) ?>">anzeigen</a></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
<?php endif ?>
