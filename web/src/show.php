<?php
    require_once __DIR__ . '/credentials.php';
    $pdo = new PDO(MARIA_DSN, MARIA_USER, MARIA_PASS, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $loadStudent = $pdo->prepare('SELECT * FROM `student_person` WHERE `id` = ?');
    $listLectures = $pdo->prepare('SELECT * FROM `student_vorlesung` WHERE `student_id` = ?');
    $loadStudent->execute(array($_GET['id']));
    $listLectures->execute(array($_GET['id']));
    $student = $loadStudent->fetch(PDO::FETCH_ASSOC);
    $listLectures->setFetchMode(PDO::FETCH_ASSOC);
?>
<table>
    <tbody>
        <tr>
            <th>name</th>
            <td><?php echo htmlspecialchars($student['name']) ?></td>
        </tr>
        <tr>
            <th>geschl</th>
            <td><?php echo htmlspecialchars($student['geschl']) ?></td>
        </tr>
        <tr>
            <th>geb</th>
            <td><?php echo htmlspecialchars($student['geb']) ?></td>
        </tr>
        <tr>
            <th>geb_ort</th>
            <td><?php echo htmlspecialchars($student['geb_ort']) ?></td>
        </tr>
        <tr>
            <th>geb_land</th>
            <td><?php echo htmlspecialchars($student['geb_land']) ?></td>
        </tr>
        <tr>
            <th>staatsbürgerschaft</th>
            <td><?php echo htmlspecialchars($student['staatsbürgerschaft']) ?></td>
        </tr>
        <tr>
            <th>volkszugehoerigkeit</th>
            <td><?php echo htmlspecialchars($student['volkszugehoerigkeit']) ?></td>
        </tr>
        <tr>
            <th>mspr</th>
            <td><?php echo htmlspecialchars($student['mspr']) ?></td>
        </tr>
        <tr>
            <th>rel</th>
            <td><?php echo htmlspecialchars($student['rel']) ?></td>
        </tr>
        <tr>
            <th>vater</th>
            <td><?php echo htmlspecialchars($student['vater']) ?></td>
        </tr>
        <tr>
            <th>wohnadr_stud</th>
            <td><?php echo htmlspecialchars($student['wohnadr_stud']) ?></td>
        </tr>
        <tr>
            <th>schule_zuletzt</th>
            <td><?php echo htmlspecialchars($student['schule_zuletzt']) ?></td>
        </tr>
        <tr>
            <th>vormund</th>
            <td><?php echo htmlspecialchars($student['vormund']) ?></td>
        </tr>
        <tr>
            <th>biogr</th>
            <td><?php echo htmlspecialchars($student['biogr']) ?></td>
        </tr>
        <tr>
            <th>prom</th>
            <td><?php echo htmlspecialchars($student['prom']) ?></td>
        </tr>
        <tr>
            <th>literaturhinweise</th>
            <td><?php echo htmlspecialchars($student['literaturhinweise']) ?></td>
        </tr>
        <tr>
            <th>anmerkung</th>
            <td><?php echo htmlspecialchars($student['anmerkung']) ?></td>
        </tr>
        <tr>
            <th>zeit</th>
            <td><?php echo htmlspecialchars($student['zeit']) ?></td>
        </tr>
    </tbody>
</table>
<table>
    <thead>
        <tr>
            <th>semester</th>
            <th>semester_nummer</th>
            <th>lecturer</th>
            <th>class_extra</th>
            <th>anmerkungen_vch_und_chf</th>
        </tr>
    </thead>
    <tbody>
        <?php /** @var array $lecture */ foreach ($listLectures as $lecture): ?>
            <tr>
                <td><?php echo htmlspecialchars($lecture['semester']) ?></td>
                <td><?php echo htmlspecialchars($lecture['semester_nummer']) ?></td>
                <td><?php echo htmlspecialchars($lecture['lecturer']) ?></td>
                <td><?php echo htmlspecialchars($lecture['class_extra']) ?></td>
                <td><?php echo htmlspecialchars($lecture['anmerkungen_vch_und_chf']) ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
