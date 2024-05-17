<?php declare(strict_types=1);
include_once __DIR__ . '/util.php';
foreach (explode(PHP_EOL, file_get_contents(__DIR__ . '/data/person-semester.txt')) as $semester) {
    [$min, $max] = detectYearMinMax($semester);
    if (!$min || !$max || $max < $min || $max > $min + 1) printf("%s: %s .. %s\n", $semester, $min, $max);
}
foreach (explode(PHP_EOL, file_get_contents(__DIR__ . '/data/person-geb.txt')) as $dob) {
    [$min, $max] = detectDOBMinMax($dob, false, 1930);
    if (!$min || !$max) printf("%s: %s..%s\n", $dob, $min, $max);
}
