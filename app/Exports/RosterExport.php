<?php

namespace App\Exports;

use App\Models\Roster;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * Optional Excel backup export for a Roster.
 *
 * Usage (in controller or service):
 *   return Excel::download(new RosterExport($roster), "roster-{$roster->id}.xlsx");
 *
 * Or store to disk:
 *   Excel::store(new RosterExport($roster), "rosters/exports/{$roster->id}.xlsx", 'public');
 */
class RosterExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    private Roster $roster;

    public function __construct(Roster $roster)
    {
        // Eager-load everything needed in one shot
        $roster->loadMissing([
            'event',
            'organization',
            'organization.coach',
            'students' => fn ($q) => $q->with('team')->orderBy('name'),
        ]);

        $this->roster = $roster;
    }

    // ── Data ──────────────────────────────────────────────────────────────

    public function collection()
    {
        return $this->roster->students;
    }

    public function headings(): array
    {
        return [
            '#',
            'Student Name',
            'Age',
            'Grade',
            'Team',
            'Shirt Size',
            'Organization',
            'Event',
            'Attendance Status',
        ];
    }

    public function map($student): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $student->name,
            $student->age        ?? '',
            $student->grade      ?? '',
            $student->team?->name ?? '',
            strtoupper($student->shirt_size ?? ''),
            $this->roster->organization?->name ?? '',
            $this->roster->event?->name ?? '',
            $student->pivot?->attendance_status ?? 'pending',
        ];
    }

    // ── Styling ───────────────────────────────────────────────────────────

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row — bold + background
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E3A5F'],
                ],
                'font' => [
                    'bold'  => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Roster #' . $this->roster->id;
    }
}