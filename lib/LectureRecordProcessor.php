<?php declare(strict_types=1);
namespace rekdagyothub;

class LectureRecordProcessor extends RecordProcessor
{
    public function processRecord(array $record, $index)
    {
        list(
            $merged,
            $id,
            $wsSs,
            $semester,
            $dozent,
            $vorlesung,
            $anmerkung
            ) = $record;
        print_r([$dozent, $this->decomposeUnicode($dozent)]);
    }
}
