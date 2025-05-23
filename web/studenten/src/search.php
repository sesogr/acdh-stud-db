<?php
    require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/connect/verbind.php');
    require_once __DIR__ . '/UnicodeString.php';
    $pageSize = 100;
    $pageNo = 0;
    $pdo = new PDO($dsndb2, $dbuser, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
        $loadSemesterRange = $pdo->query(
        <<<'EOD'
        SELECT
            MIN(CAST(REGEXP_SUBSTR(semester_abs, '[0-9]{4}') AS UNSIGNED)) AS semester_begin,
            MAX(
                if(semester_abs like 'W%%', 1, 0) +
                CAST(REGEXP_SUBSTR(semester_abs, '[0-9]{4}') AS UNSIGNED)
            ) AS semester_end
        FROM student_attendance
        WHERE
            semester_abs IS NOT NULL
            AND REGEXP_SUBSTR(semester_abs, '[0-9]{4}') != ''
        EOD
    );
        list($minSemesterYear, $maxSemesterYear) = $loadSemesterRange->fetch(PDO::FETCH_NUM);
        $params['globalMaxYear'] = $maxSemesterYear;
        $params['globalMinYear'] = $minSemesterYear;
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
    i.person_id,
    last_name,
    ln.is_doubtful as name_doubtful,
    given_names,
    birth_place,
    birth_country_historic,
    birth_country_today,
    bp.is_doubtful as birth_place_doubtful,
    birth_date,
    is_from_supplemental_data_source,
    bd.is_doubtful as birth_date_doubtful,
    gender,
    language,
    religion
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
AND (REGEXP_SUBSTR(semester_abs, '[0-9]{4}') + if(semester_abs like 'W%%', 0.5, 0.0) BETWEEN :begin and :end
OR REGEXP_SUBSTR(semester_rel,'[0-9]+') + i.year_min - 1 BETWEEN :begin and :end
OR i.year_min BETWEEN :begin and :end
OR i.year_max BETWEEN :begin and :end
OR ((:globalMinYear = :begin and :globalMaxYear = :end) and i.year_min is null and i.year_max is null)
)

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
<div class="new-search"><a href="/demos_suche_studenten.php" target="_self">Neue Suche...</a></div>
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
    <table class="data-table">
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
                        ?>><?php echo htmlspecialchars(
                            preg_match('/00:/', $student['birth_date'])
                                ? substr($student['birth_date'], 0, -8)
                                : $student['birth_date']
                        ) ?></td>
                    <td><?php echo htmlspecialchars($student['birth_place']) ?></td>
                    <td><?php echo htmlspecialchars($student['birth_country_historic']) ?></td>
                    <td><?php echo htmlspecialchars($student['birth_country_today']) ?></td>
                    <td><a href="?id=<?php echo htmlspecialchars($student['person_id']) ?>">anzeigen</a></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
<?php endif ?>
