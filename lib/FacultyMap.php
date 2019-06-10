<?php declare(strict_types=1);
namespace rekdagyothub;

interface FacultyMap
{
    public function getFacultyForPersonId(int $id): ?string;
}
