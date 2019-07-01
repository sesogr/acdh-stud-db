<?php declare(strict_types=1);
namespace rekdagyothub;
use SplFileObject;

class TsvReader extends SplFileObject
{
    public function __construct($file_name, $open_mode = 'r', $use_include_path = false, $context = null)
    {
        parent::__construct($file_name, $open_mode, $use_include_path, $context);
        $this->setCsvControl("\t", '"', '"');
        $this->setFlags(
            SplFileObject::DROP_NEW_LINE
            | SplFileObject::READ_AHEAD
            | SplFileObject::READ_CSV
            | SplFileObject::SKIP_EMPTY
        );
    }
}
