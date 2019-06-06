<?php declare(strict_types=1);
namespace rekdagyothub;

use UnicodeString;

abstract class RecordProcessor
{
    /** @var ImmutablePdo */
    protected $pdo;

    public function __construct(ImmutablePdo $pdo)
    {
        $this->pdo = $pdo;
        $this->pdo->exec('SET NAMES utf8mb4');
    }

    abstract public function processRecord(array $record, $index);

    protected function decomposeUnicode($input)
    {
        $unicodeString = new UnicodeString();
        $unicodeString->loadUtf8String(
            trim(
                preg_replace(
                    '/[^a-z\\x80-\\xff]+/i',
                    ' ',
                    str_replace(['(', ')', '[', ']', '{', '}', '<', '>'], '', $input)
                )
            )
        );
        return $unicodeString
            ->decompose(true)
            ->filter(null, [UnicodeString::LETTER, UnicodeString::SEPARATOR_SPACE])
            ->toLowerCase()
            ->saveUtf8String();
    }
}
