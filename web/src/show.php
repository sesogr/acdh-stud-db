<?php
    require_once __DIR__ . '/credentials.php';
    function out ($string, $markIllegible = false) {
        echo $markIllegible
            ? preg_replace('/x{2,}/i', '<span title="unleserlich" class="illegible">###</span>', htmlspecialchars($string))
            : htmlspecialchars($string);
    }
    $pdo = new PDO(MARIA_DSN, MARIA_USER, MARIA_PASS, array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ));
    $pdo->exec('SET NAMES utf8');
    $listProperties = $pdo->prepare('SELECT * FROM `v_student_complete` WHERE `person_id` = ?');
    $listLectures = $pdo->prepare('SELECT * FROM `student_attendance` WHERE `person_id` = ? ORDER BY substr(`semester_abs` FROM 4), `lecturer`');
    $listProperties->execute(array($_GET['id']));
    $listLectures->execute(array($_GET['id']));
    $student = array();
    $hasTimes = false;
    foreach ($listProperties as $property) {
        $hasTimes = $hasTimes
            || $property['times']
            || in_array($property['property'], array('biography', 'birth_date')) && $property['value2'] == 'true';
        $student[$property['property']][] = array(
            'value' => sprintf(
                $property['property'] == 'birth_place'
                    ? '%s (hist.: %s / heute: %s)'
                    : '%s',
                $property['value'],
                $property['value2'],
                $property['value3']
            ),
            'time' => sprintf(
                in_array($property['property'], array('biography', 'birth_date')) && $property['value2'] == 'true'
                    ? '[Aus zusätzlichen Quellen ergänzt]'
                    : '%s%s',
                $property['is_doubtful'] ? '[ungewiss] ' : '',
                $property['times']
            ),
            'doubtful' => !!$property['is_doubtful']
        );
    }
    $fields = array(
        'last_name' => 'Name',
        'given_names' => 'Vorname',
        'gender' => 'Geschlecht',
        'birth_date' => 'Geburtsdatum',
        'birth_place' => 'Geburtsort',
        'nationality' => 'Staatsbürgerschaft',
        'ethnicity' => 'Volkszugehörigkeit',
        'language' => 'Muttersprache',
        'religion' => 'Religion',
        'father' => 'Vater',
        'studying_address' => 'Wohnadr. (Studium)',
        'last_school' => 'Letzte Schule',
        'guardian' => 'Vormund',
        'biography' => 'Biographie',
        'graduation' => 'Promotion',
        'literature' => 'Literaturhinweise',
        'remarks' => 'Bemerkungen'
    );
?>
<table>
    <tbody>
        <?php foreach ($fields as $field => $title): ?>
            <?php if (isset($student[$field])): ?>
                <?php foreach ($student[$field] as $index => $value): ?>
                    <tr>
                        <?php if ($index === 0): ?>
                            <th rowspan="<?php echo count($student[$field]) ?>"><?php out($title) ?></th>
                        <?php endif ?>
                        <td><?php out($value['value'], $value['doubtful']) ?></td>
                        <?php if ($hasTimes): ?>
                            <td><?php out($value['time']) ?></td>
                        <?php endif ?>
                    </tr>
                <?php endforeach ?>
            <?php else: ?>
                <tr>
                    <th><?php out($title) ?></th>
                    <td></td>
                    <?php if ($hasTimes): ?>
                        <td></td>
                    <?php endif ?>
                </tr>
            <?php endif ?>
        <?php endforeach ?>
    </tbody>
</table>
<table>
    <thead>
        <tr>
            <th>Semester</th>
            <th>ordinal</th>
            <th>Fakultät</th>
            <th>Dozent</th>
            <th>Vorlesung</th>
            <th>Bemerkungen</th>
        </tr>
    </thead>
    <tbody>
        <?php /** @var array $lecture */ foreach ($listLectures as $lecture): ?>
            <tr>
                <td><?php out($lecture['semester_abs']) ?></td>
                <td><?php out($lecture['semester_rel']) ?></td>
                <td><?php out($lecture['faculty']) ?></td>
                <td><?php out($lecture['lecturer']) ?></td>
                <td><?php out($lecture['class']) ?></td>
                <td><?php out($lecture['remarks']) ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
