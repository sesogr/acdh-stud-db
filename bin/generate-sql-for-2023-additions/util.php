<?php declare(strict_types=1);
function detectYearMinMax(?string $semester)
{
    if ($semester && preg_match("/(\\d+)(?:\\D+(\\d+))?/", $semester, $matches)) {
        return [$matches[1], substr($matches[1], 0, 4 - strlen($matches[2] ?? '')) . ($matches[2] ?? '')];
    }
    return [null, null];
}

function detectDOBMinMax(?string $dateOfBirth, bool $isDoubtful, ?int $semester)
{
    $dobCleaned = preg_replace('/[\\[\\]?]/', '', $dateOfBirth, -1, $count);
    if ($count || $isDoubtful) {
        return [sprintf("%04d-01-01", $semester - 90), sprintf("%04d-01-01", $semester - 10)];
    }
    if (preg_match("/^(\\d{4}-\\d{2}-\\d{2})(?: .+)?$/", $dobCleaned, $matches)) {
        return [$matches[1], $matches[1]];
    }
    if (preg_match("/^(\\d{1,2})\\.(\\d{1,2})\\.(\\d{4})$/", $dobCleaned, $matches)) {
        $date = sprintf("%04d-%02d-%02d", $matches[3], $matches[2], $matches[1]);
        return [$date, $date];
    }
    if (preg_match("/^(?:geb\\. )?(ca\\. )?(\\d{4})(?:\\/(\\d{4}))?$/", $dobCleaned, $matches)) {
        $spread = $matches[1] ? 3 : 0;
        return [sprintf("%04d-01-01", $matches[2] - $spread), sprintf("%04d-12-31", ($matches[3] ?? $matches[2]) + $spread)];
    }
    return [null, null];
}
