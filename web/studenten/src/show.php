<?php
    require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/connect/verbind.php');
    $pdo = new PDO($dsndb2, $dbuser, $dbpassword, array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ));
    $pdo->exec('SET NAMES utf8');
    $sumPropertyWeights = $pdo->query('select sum(weight) from student_similarity_weight', PDO::FETCH_COLUMN, 0)->fetch();
    $listProperties = $pdo->prepare("SELECT * FROM `v_student_complete` WHERE ? like concat_ws(',', '%', `person_id`, '%')");
    $listLectures = $pdo->prepare("SELECT * FROM `student_attendance` WHERE ? like concat_ws(',', '%', `person_id`, '%') ORDER BY `semester_abs` = '', substr(`semester_abs` FROM 4), `semester_rel`");
    $listSimilarStudents = $pdo->prepare(<<<'EOD'
        select
            if(id_low = ?, id_high, id_low) other_id,
            sum(sg.mean * sw.weight) / ? weighted_mean,
            avg(median) median,
            min(min) min,
            max(max) max,
            count(sg.property) count
        from `student_similarity_graph` sg
            join student_similarity_weight sw using (property)
        where (`id_low` = ? or `id_high` = ?)
            and `max` > .5
        group by other_id
        having `weighted_mean` > .33
        order by weighted_mean desc
        limit 5
    EOD
    );
    $listSimilarStudents->execute(array($_GET['id'], $sumPropertyWeights, $_GET['id'], $_GET['id']));
    $similarStudents = $listSimilarStudents->fetchAll();
    $showDupes = count($similarStudents);
    $similarIds = array_map(function ($record) { return $record['other_id']; }, $similarStudents);
    array_unshift($similarIds, $_GET['id']);
    $listProperties->execute(array(sprintf(",%s,", implode(",", $similarIds))));
    $listLectures->execute(array(sprintf(",%s,", implode(",", $similarIds))));
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
            'doubtful' => !!$property['is_doubtful'],
            'id' => $property['person_id']
        );
    }
    $lecturefields = array(
        'semester_abs' => 'Semester',
        'semester_rel' => 'ordinal',
        'faculty' => 'Fakultät',
        'lecturer' => 'Dozent',
        'class' => 'Vorlesung',
        'remarks' => 'Bemerkungen'
    );
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
    if ($showDupes): ?>
    <p>Es wurden <?php echo htmlspecialchars(count($similarStudents)) ?> mögliche Duplikate gefunden:</p>
    <ul class="dupes">
        <?php if ($showDupes > 1): ?>
            <li>
                <input type="checkbox" checked="checked" onclick="changeall(this)" />
                <a> ALLE </a>
            </li>
        <?php endif ?>
        <?php foreach ($similarStudents as $index => $record): ?>
            <li>
                <input type="checkbox" checked="checked"
                       data-dupe-id="<?php echo htmlspecialchars(chr(98 + $index)); ?>" onclick="showhidetoggle(this)"/>
                <a class=<?php echo htmlspecialchars(chr(98 + $index)); ?> href="?id=<?php echo htmlspecialchars($record['other_id']); ?>"><?php echo htmlspecialchars(chr(66 + $index)); ?></a>: <?php echo htmlspecialchars(sprintf('%.0f%%', 100 * $record['weighted_mean'])); ?>

            </li>
        <?php endforeach ?>
    </ul>
<?php endif ?>
<table class="data-table">
    <tbody>
    <?php foreach ($fields as $field => $title): ?>
        <?php if (isset($student[$field])): ?>
            <?php foreach ($student[$field] as $index => $value): ?>
                <?php $index_ID = array_search($value['id'], $similarIds) ?>
                <tr class="<?php echo htmlspecialchars(chr(97 + $index_ID)); ?>">
                    <?php if ($index === 0): ?>
                        <th rowspan="<?php echo count($student[$field]) ?>"><?php echo htmlspecialchars($title); ?></th>
                    <?php endif ?>
                    <?php if ($showDupes): ?>
                        <td class="<?php echo htmlspecialchars($value['id'] == $_GET['id'] ? 'orig' : 'dupe'); ?>">
                            <span class="<?php echo htmlspecialchars(chr(97 + $index_ID)); ?>"><?php echo htmlspecialchars(chr(65 + $index_ID)); ?></span>
                        </td>
                    <?php endif ?>
                    <td>
                        <?php
                        if ($field == "birth_date") {
                            if (preg_match('/00:/', $value['value'])) {
                                echo htmlspecialchars(substr($value['value'], 0, -8));
                            } else {
                                echo htmlspecialchars($value['value']);
                            }
                        } else {
                            echo htmlspecialchars($value['value']);
                        }
                        ?>
                    </td>
                    <?php if ($hasTimes): ?>
                        <td><?php echo htmlspecialchars($value['time']); ?></td>
                    <?php endif ?>
                </tr>
            <?php endforeach ?>
        <?php else: ?>
            <tr>
                <th><?php echo htmlspecialchars($title); ?></th>
                <td></td>
                <?php if ($hasTimes): ?>
                    <td></td>
                <?php endif ?>
            </tr>
        <?php endif ?>
    <?php endforeach ?>
    </tbody>
</table>
<table class="data-table">
    <thead>
    <tr>
        <?php if ($showDupes): ?>
            <th></th>
        <?php endif ?>
        <?php foreach ($lecturefields as $field => $title): ?>
            <th><?php echo htmlspecialchars($title); ?></th>
        <?php endforeach ?>
    </tr>
    </thead>
    <tbody>
    <?php /** @var array $lecture */
    foreach ($listLectures as $lecture): ?>
        <?php $index_Lecture = array_search($lecture['person_id'], $similarIds) ?>
        <tr class="<?php echo htmlspecialchars(chr(97 + $index_Lecture)); ?>">
            <?php if ($showDupes): ?>
                <td>
                    <span class="<?php echo htmlspecialchars(chr(97 + $index_Lecture)); ?>"><?php echo htmlspecialchars(chr(65 + $index_Lecture)); ?></span>
                </td>
            <?php endif ?>
            <td><?php echo htmlspecialchars($lecture['semester_abs']); ?></td>
            <td><?php echo htmlspecialchars($lecture['semester_rel']); ?></td>
            <td><?php echo htmlspecialchars($lecture['faculty']); ?></td>
            <td><?php echo htmlspecialchars($lecture['lecturer']); ?></td>
            <td><?php echo htmlspecialchars($lecture['class']); ?></td>
            <td><?php echo htmlspecialchars($lecture['remarks']); ?></td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>
