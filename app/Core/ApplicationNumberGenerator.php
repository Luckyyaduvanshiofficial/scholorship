<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Simple application number formatter.
 * Format: TSVS-{year}-{id}
 * MySQL AUTO_INCREMENT handles the counter automatically.
 */
class ApplicationNumberGenerator
{
    /**
     * Format an application ID into the readable format.
     * Call this in the view when displaying application_no.
     *
     * @param int $id           Application row ID
     * @param string $year      e.g. "2026"
     * @param int $typeId       1=Scholarship(TSVS), 2=Pratibha(TSVP)
     * @return string           e.g. "TSVS-2026-000042"
     */
    public static function format(int $id, string $year, int $typeId = 1): string
    {
        $prefix = ($typeId === 2) ? 'TSVP' : 'TSVS';
        return sprintf('%s-%s-%06d', $prefix, $year, $id);
    }
}
