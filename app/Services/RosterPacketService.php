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
        $pdf = Pdf::loadView('roster.pdf.field_packet', [
            'roster'      => $roster,
            'grouped'     => $grouped,
            'generatedAt' => now()->format('M d, Y H:i'),
        ])
        ->setPaper('a4', 'landscape')
        ->setOptions([
            /*
             * FIX: Use DejaVu fonts — they are bundled with DomPDF and render
             * correctly. Do NOT set defaultFont to Arial/Helvetica; those are
             * not embedded and DomPDF will fall back to a bitmap glyph that
             * looks blurry and cuts off at the right margin.
             */
            'defaultFont'          => 'DejaVu Sans',

            /*
             * FIX: Higher DPI = crisper text and borders. 150 is the sweet
             * spot for A4 landscape — readable but not slow to generate.
             */
            'dpi'                  => 150,

            /*
             * FIX: chroot must be set so DomPDF resolves relative asset paths
             * correctly and doesn't silently truncate the page content.
             */
            'chroot'               => public_path(),

            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,

            /*
             * FIX: Enable font subsetting so glyphs outside Latin-1 (em-dash,
             * checkmark symbol in the template) render properly.
             */
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
        $payload = json_encode([
            'roster_id'       => $roster->id,
            'event_id'        => $roster->event_id,
            'organization_id' => $roster->organization_id,
            'checksum'        => $this->buildChecksum($roster),
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
}