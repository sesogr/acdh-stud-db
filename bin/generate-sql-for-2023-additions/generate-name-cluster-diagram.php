<?php declare(strict_types=1);
namespace rekdagyothub;

require_once __DIR__ . '/../../vendor/autoload.php';
function levRel($a, $b)
{
    return 6 * levenshtein($a, $b) / (strlen($a) + strlen($b) ?: 1);
}

$personRecords = XlsxReader::iterateRecords(__DIR__ . '/../../files/2023/Studierende Muwi WS 1927 28 Personen_MWA_5.10._ (4).xlsx');
$nameClusters = [];
foreach ($personRecords as $record) {
    if ($record['X-serial-number']) {
        $nameClusters[trim(preg_replace('/\\PL+/u', ' ', strval($record['name'])))][] = $record;
    }
}
$names = array_keys($nameClusters);
rsort($names);
printf("@startuml\nleft to right direction\nhide @unlinked\n");
foreach ($names as $k => $name) {
    printf(
        "object \"%s\" as n%03d {\n  %s\n}\n",
        $name,
        $k,
        implode(
            "\n  ",
            array_map(
                fn($r) => sprintf('%s: %s %s', $r['X-serial-number'], $r['geb'], $r['geb-ort']),
                $nameClusters[$name]
            )
        )
    );
}
foreach ($names as $k1 => $name1) {
    $n1 = preg_split('/\\PL+/u', $name1, 0, PREG_SPLIT_NO_EMPTY);
    foreach (array_slice($names, $k1 + 1) as $k2 => $name2) {
        $n2 = preg_split('/\\PL+/u', $name2, 0, PREG_SPLIT_NO_EMPTY);
        $maxNameLength = max(count($n1), count($n2));
        $result = array_uintersect($n1, $n2, 'rekdagyothub\levRel');
        if (isset($result[0]) && count($result) > 1) {
            $dist = levRel($n1[0], $n2[0]) + levRel(implode(' ', array_slice($n1, 1)), implode(' ', array_slice($n2, 1))) / 3;
            if (true || $dist >= .01 || count($n1) !== count($n2)) {
                $deviation = .333 * $dist + .5 * ($maxNameLength - count($result)) / $maxNameLength;
                printf("n%03d --%s n%03d: %d\n", $k1, ''/*str_repeat('-', $deviation * 5 % 10)*/, $k1 + $k2 + 1, $deviation * 100);
            }
        }
    }
}
printf("@enduml\n");
