<?php
    require_once __DIR__ . '/credentials.php';
    require_once __DIR__ . '/out.php';
    $pdo = new PDO(MARIA_DSN, MARIA_USER, MARIA_PASS, array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ));
    $pdo->exec('SET NAMES utf8');
    $sumPropertyWeights = $pdo->query('select sum(weight) from student_similarity_weight', PDO::FETCH_COLUMN, 0)->fetch();
    $listProperties = $pdo->prepare("SELECT * FROM `v_student_complete` WHERE ? like concat_ws(',', '%', `person_id`, '%')");
    $listLectures = $pdo->prepare("SELECT * FROM `student_attendance` WHERE ? like concat_ws(',', '%', `person_id`, '%')");
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
            and `sg`.`property` <> 'birth_date'
        group by other_id
        having `weighted_mean` > .3
        order by weighted_mean desc
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
        'person_id' => 'Student',
        'semester_abs' => 'Semester',
        'semester_rel' => 'Vorname',
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
    <p>Es wurden <?php out(count($similarStudents)) ?> mögliche Duplikate gefunden:</p>
    <ul class="dupes">
        <?php foreach ($similarStudents as $index => $record): ?>
            <li>
                <a class=<?php out(chr(98 + $index))?> href="?id=<?php out($record['other_id']) ?>"><?php out(chr(66 + $index)) ?></a>: <?php out(sprintf('%.0f%%', 100 * $record['weighted_mean'])) ?>
                <input type="checkbox" checked="checked" data-dupe-id="<?php out(chr(98 + $index))?>" onclick=showhidetoggle(this) />
            </li>
        <?php endforeach ?>
    </ul>
<?php endif ?>
<table>
    <tbody>
        <?php foreach ($fields as $field => $title): ?>
            <?php if (isset($student[$field])): ?>
                <?php foreach ($student[$field] as $index => $value): ?>
                    <tr class="<?php out(chr(97 + array_search($value['id'], $similarIds))) ?>">
                        <?php if ($index === 0): ?>
                            <th rowspan="<?php echo count($student[$field]) ?>"><?php out($title) ?></th>
                        <?php endif ?>
                        <?php if ($showDupes): ?>
                            <td class="<?php out($value['id'] == $_GET['id'] ? 'orig' : 'dupe') ?>">
                                <span class="<?php out(chr(97 + array_search($value['id'], $similarIds))) ?>"><?php out(chr(65 + array_search($value['id'], $similarIds))) ?></span>
                            </td>
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
        <?php foreach ($lecturefields as $field => $title ): ?>
            <th><?php out($title)?></th>
        <?php endforeach ?>
    </thead>
    <tbody>
        <?php /** @var array $lecture */ foreach ($listLectures as $lecture): ?>
            <tr class="<?php out(chr(97 + array_search($lecture['person_id'], $similarIds)))?>" >
                <td>
                    <span class="<?php out(chr(97 + array_search($lecture['person_id'], $similarIds))) ?>"><?php out(chr(65 + array_search($lecture['person_id'], $similarIds))) ?></span>
                    <?php out($lecture['person_id']) ?>
                </td>
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
