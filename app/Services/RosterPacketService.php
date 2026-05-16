<?php

namespace App\Services;

use App\Models\Roster;
use App\Models\RosterStudent;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RosterPacketService
{
    private const DISK    = 'public';
    private const PDF_DIR = 'roster/pdfs';
    private const QR_DIR  = 'roster/qrcodes';

    /**
     * Generate the full field packet for a roster:
     *   1. PDF roster sheet
     *   2. QR code image
     *   3. Status → "ready"
     *
     * @param  int  $rosterId
     * @return array{pdf_path:string, qr_path:string, pdf_url:string, qr_url:string}
     *
     * @throws \RuntimeException on failure
     */
    public function generate(int $rosterId): array
    {
        $roster = $this->loadRoster($rosterId);

        $grouped = $this->groupStudentsByTeam($roster);

        $pdfPath = $this->generatePdf($roster, $grouped);
        $qrPath  = $this->generateQr($roster);

        $roster->update(['status' => 'ready']);

        $pdfUrl = Storage::disk(self::DISK)->url($pdfPath);
        $qrUrl  = Storage::disk(self::DISK)->url($qrPath);

        Log::info("RosterPacket generated for roster #{$rosterId}", [
            'pdf' => $pdfPath,
            'qr'  => $qrPath,
        ]);

        return [
            'pdf_path' => $pdfPath,
            'qr_path'  => $qrPath,
            'pdf_url'  => $pdfUrl,
            'qr_url'   => $qrUrl,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CHECK-IN
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Bulk check-in: roster → "checked-in", all students → "present".
     * Chunked to avoid memory overflow on large rosters.
     */
    public function checkIn(Roster $roster): void
    {
        $roster->update(['status' => 'checked-in']);

        RosterStudent::where('roster_id', $roster->id)
            ->chunkById(500, function ($chunk) {
                $ids = $chunk->pluck('id')->toArray();
                RosterStudent::whereIn('id', $ids)
                    ->update(['attendance_status' => 'present']);
            });

        Log::info("Roster #{$roster->id} checked in. All students marked present.");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PDF
    // ─────────────────────────────────────────────────────────────────────────
    private function generatePdf(Roster $roster, array $grouped): string
    {
        $qrFilePath = public_path("roster/qrcodes/{$roster->id}.svg");
    
        // 🔥 IMPORTANT: convert QR to base64 so DomPDF can render it
        $qrBase64 = null;
    
        if (file_exists($qrFilePath)) {
            $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($qrFilePath));
        }
    
        $pdf = Pdf::loadView('roster.pdf.field_packet', [
            'roster'      => $roster,
            'grouped'     => $grouped,
            'generatedAt' => now()->format('M d, Y H:i'),
            'qrBase64'    => $qrBase64, // ✅ send base64 instead of path
        ])
        ->setPaper('a4', 'landscape')
        ->setOptions([
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 150,
            'chroot' => public_path(),
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true, // 🔥 IMPORTANT change
            'isFontSubsettingEnabled' => true,
        ]);
    
        $relativePath = self::PDF_DIR . "/{$roster->id}.pdf";
    
        $filePath = public_path('roster/pdfs/' . $roster->id . '.pdf');
    
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
        }
    
        file_put_contents($filePath, $pdf->output());
    
        return $relativePath;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // QR CODE
    // ─────────────────────────────────────────────────────────────────────────

    private function generateQr(Roster $roster): string
    {
        $payload = config('app.url') . '/checkin?' . http_build_query([
            'roster_id' => $roster->id,
            'checksum'  => $this->buildChecksum($roster),
        ]);
        $qrImage = QrCode::format('svg')
            ->size(300)
            ->errorCorrection('H')  // High error correction — better for field scanning
            ->generate($payload);

        // FIX: extension was .png in qrExists()/qrUrl() but file saved as .svg — aligned to .svg
        $relativePath = self::QR_DIR . "/{$roster->id}.svg";

        $filePath = public_path('roster/qrcodes/' . $roster->id . '.svg');

        if (!file_exists(dirname($filePath))) { 
            mkdir(dirname($filePath), 0777, true);
        }
        
        file_put_contents($filePath, $qrImage);

        return $relativePath;
    }

    /**
     * HMAC-SHA256 checksum so QR payloads can be verified server-side.
     */
    public function buildChecksum(Roster $roster): string
    {
        return hash_hmac(
            'sha256',
            "{$roster->id}:{$roster->event_id}:{$roster->organization_id}",
            config('app.key')
        );
    }

    /**
     * Verify a checksum received from a scanned QR payload.
     */
    public function verifyChecksum(Roster $roster, string $incoming): bool
    {
        return hash_equals($this->buildChecksum($roster), $incoming);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    private function loadRoster(int $rosterId): Roster
    {
        return Roster::with([
            'event',
            'organization',
            'organization.coach',
            'students' => fn ($q) => $q->with('team')->orderBy('name'),
        ])->findOrFail($rosterId);
    }

    private function groupStudentsByTeam(Roster $roster): array
    {
        $grouped = [];

        foreach ($roster->students as $student) {
            $teamName = $student->team?->name ?? 'Unassigned';
            $grouped[$teamName][] = $student;
        }

        ksort($grouped);

        return $grouped;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // FILE HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    public function pdfExists(int $rosterId): bool
    {
        return file_exists(public_path("roster/pdfs/{$rosterId}.pdf"));
    }

    public function qrExists(int $rosterId): bool
    {
        return file_exists(public_path("roster/qrcodes/{$rosterId}.svg"));
    }

    public function pdfUrl(int $rosterId): string
    {
        return url("roster/pdfs/{$rosterId}.pdf");
    }

    public function qrUrl(int $rosterId): string
    {
        return url("roster/qrcodes/{$rosterId}.svg");
    }

    /**
 * Selective check-in: marks the roster "checked-in".
 * Students whose IDs are in $presentIds → "present".
 * All other students in this roster → "absent".
 *
 * @param  Roster  $roster
 * @param  int[]   $presentIds  Array of RosterStudent IDs marked present by admin
 */
public function checkInSelective(Roster $roster, array $presentStudentIds): void
{
    $rosterId = $roster->id;
 
    // ── Log exactly what we received ──────────────────────────────────────
    Log::info("checkInSelective START — Roster #{$rosterId}", [
        'received_present_student_ids' => $presentStudentIds,
        'present_count_received'       => count($presentStudentIds),
    ]);
 
    // ── Mark roster checked-in ────────────────────────────────────────────
    $roster->update(['status' => 'checked-in']);
 
    // ── Mark present ──────────────────────────────────────────────────────
    $markedPresent = 0;
    if (!empty($presentStudentIds)) {
        $markedPresent = RosterStudent::where('roster_id', $rosterId)
            ->whereIn('student_id', $presentStudentIds)   // ← student_id FK, not PK
            ->update(['attendance_status' => 'present']);
    }
 
    // ── Mark absent ───────────────────────────────────────────────────────
    $markedAbsent = RosterStudent::where('roster_id', $rosterId)
        ->when(
            !empty($presentStudentIds),
            fn ($q) => $q->whereNotIn('student_id', $presentStudentIds)  // ← student_id FK
        )
        ->update(['attendance_status' => 'absent']);
 
    // ── Log the actual DB outcome ─────────────────────────────────────────
    // Re-query so the log reflects what actually landed in the database.
    $dbPresent = RosterStudent::where('roster_id', $rosterId)
        ->where('attendance_status', 'present')
        ->pluck('student_id')
        ->toArray();
 
    $dbAbsent = RosterStudent::where('roster_id', $rosterId)
        ->where('attendance_status', 'absent')
        ->pluck('student_id')
        ->toArray();
 
    Log::info("checkInSelective DONE — Roster #{$rosterId}", [
        'rows_marked_present'       => $markedPresent,
        'rows_marked_absent'        => $markedAbsent,
        'db_present_student_ids'    => $dbPresent,
        'db_absent_student_ids'     => $dbAbsent,
        'db_present_count'          => count($dbPresent),
        'db_absent_count'           => count($dbAbsent),
    ]);
}
}