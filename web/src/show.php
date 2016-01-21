<?php
    require_once __DIR__ . '/credentials.php';
    $pdo = new PDO(MARIA_DSN, MARIA_USER, MARIA_PASS, array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ));
    $listProperties = $pdo->prepare('SELECT * FROM `v_student_complete` WHERE `person_id` = ?');
    $listLectures = $pdo->prepare('SELECT * FROM `student_attendance` WHERE `person_id` = ?');
    $listProperties->execute(array($_GET['id']));
    $listLectures->execute(array($_GET['id']));
    $student = array();
    foreach ($listProperties as $property) {
        $student[$property['property']][] = array(
            'value' => sprintf(
                $property['value2'] ? '%s (hist.: %s / heute: %s)' : '%s',
                $property['value'],
                $property['value2'],
                $property['value3']
            ),
            'time' => $property['times']
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
                            <th rowspan="<?php echo count($student[$field]) ?>"><?php echo htmlspecialchars($title) ?></th>
                        <?php endif ?>
                        <td><?php echo htmlspecialchars($value['value']) ?></td>
                        <td><?php echo htmlspecialchars($value['time']) ?></td>
                    </tr>
                <?php endforeach ?>
            <?php else: ?>
                <tr>
                    <th><?php echo htmlspecialchars($title) ?></th>
                    <td></td>
                    <td></td>
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
            <th>Dozent</th>
            <th>Vorlesung</th>
            <th>Zusatz</th>
            <th>Bemerkungen</th>
        </tr>
    </thead>
    <tbody>
        <?php /** @var array $lecture */ foreach ($listLectures as $lecture): ?>
            <tr>
                <td><?php echo $lecture['semester_abs'] ?></td>
                <td><?php echo $lecture['semester_rel'] ?></td>
                <td><?php echo $lecture['lecturer'] ?></td>
                <td><?php echo $lecture['class'] ?></td>
                <td><?php echo $lecture['class_extra'] ?></td>
                <td><?php echo $lecture['remarks'] ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
