<?php declare(strict_types=1);
namespace rekdagyothub;

use Generator;
use MultipleIterator;
use Shuchkin\SimpleXLSX;

class XlsxReader
{
    private static function iterateRows(SimpleXLSX $sx): Generator
    {
        $mi = new MultipleIterator(MultipleIterator::MIT_NEED_ALL | MultipleIterator::MIT_KEYS_NUMERIC);
        $mi->attachIterator($sx->readRows());
        $mi->attachIterator($sx->readRowsEx());
        foreach ($mi as $key => [$info, $meta]) {
            yield $key[0] => array_map(
                fn($i, $m) => [$i, !!preg_match('/background-color\\s*:\\s*([^;]+)/', $m['css'])],
                $info,
                $meta
            );
        }
    }

    public static function iterateRecords(string $inputFile): Generator
    {
        $headers = [];
        foreach (self::iterateRows(SimpleXLSX::parseFile($inputFile)) as $index => $record) {
            if ($index) {
                yield $index - 1 => [
                    ...array_combine(
                        $headers,
                        array_map(fn($cell) => $cell[0], $record)
                    ),
                    '::doubtful' => array_filter($headers, fn($i) => $record[$i][1], ARRAY_FILTER_USE_KEY),
                ];
            } else {
                $headers = array_map(fn($field) => $field[0], $record);
            }
        };
    }
}
