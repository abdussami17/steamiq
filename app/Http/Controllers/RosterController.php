<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Roster;
use App\Services\RosterImportService;
use App\Services\RosterPacketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RosterController extends Controller
{
    public function __construct(
        private RosterImportService $importService,
        private RosterPacketService $packetService,
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // VIEWS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * GET /rosters
     */
    public function index()
    {
        $events = Event::orderBy('name')->get(['id', 'name']);

        return view('roster.index', compact('events'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // AJAX: ROSTER TABLE DATA
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * GET /rosters/list  (AJAX)
     */
    public function list(Request $request): JsonResponse
    {
        $query = Roster::with(['event', 'organization', 'organization.coach'])
            ->withCount('students');

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->integer('event_id'));
        }

        $rosters = $query->latest()->get()->map(fn (Roster $r) => [
            'id'            => $r->id,
            'event'         => $r->event?->name,
            'organization'  => $r->organization?->name,
            'coach'         => $r->organization?->coach?->name ?? '—',
            'total_players' => $r->students_count,
            'status'        => $r->status,
            'uploaded_at'   => $r->created_at?->format('M d, Y H:i'),
            'actions'       => [
                'view_url' => route('rosters.show', $r->id),
            ],
            'has_pdf' => $this->packetService->pdfExists($r->id),
            'has_qr'  => $this->packetService->qrExists($r->id),
            'pdf_url' => $this->packetService->pdfUrl($r->id),
            'qr_url'  => $this->packetService->qrUrl($r->id),
        ]);

        return response()->json(['data' => $rosters]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // IMPORT
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * POST /rosters/import  (AJAX)
     */
    public function import(Request $request): JsonResponse
    {
        if (! $request->user()->hasAnyRole(['admin', 'coach'])) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validated = $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'file'     => [
                'required',
                'file',
                'mimes:xlsx,csv',
                'max:10240',
            ],
        ]);

        try {
            $report = $this->importService->import(
                $request->file('file'),
                (int) $validated['event_id']
            );

            return response()->json([
                'success' => true,
                'report'  => $report,
            ]);

        } catch (\Throwable $e) {
            \Log::error('RosterImport fatal error', ['exception' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
            ], 422);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * GET /rosters/{roster}
     */
    public function show(Roster $roster): JsonResponse
    {
        $roster->load([
            'event',
            'organization.coach',
            'students.team.group',
        ]);

        return response()->json([
            'id'           => $roster->id,
            'event'        => $roster->event->name ?? '—',
            'organization' => $roster->organization->name ?? '—',
            'coach'        => $roster->organization->coach->name ?? '—',
            'status'       => $roster->status,
            'players'      => $roster->students->map(fn ($s) => [
                'name'  => $s->name,
                'age'   => $s->age,
                'grade' => $s->grade,
                'team'  => $s->team->name ?? '—',
                'group' => $s->team->group->group_name ?? '—',
            ]),
        ]);
    }

    // =========================================================================
    // PHASE 2 — Field Packet Generation
    // =========================================================================

    /**
     * POST /rosters/{roster}/generate-packet  (AJAX)
     */
    public function generateFieldPacket(Request $request, Roster $roster): JsonResponse
    {
        if (! $request->user()->hasAnyRole(['admin', 'coach'])) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        if ($roster->isCheckedIn()) {
            return response()->json([
                'success' => false,
                'message' => 'Roster is already checked-in. Cannot regenerate packet.',
            ], 422);
        }

        try {
            $result = $this->packetService->generate($roster->id);

            return response()->json([
                'success' => true,
                'status'  => 'ready',
                'pdf_url' => $result['pdf_url'],
                'qr_url'  => $result['qr_url'],
            ]);

        } catch (\Throwable $e) {
            \Log::error("Packet generation failed for roster #{$roster->id}", [
                'exception' => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Packet generation failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    // =========================================================================
    // PHASE 2 — Check-In
    // =========================================================================

    public function checkinPage(Request $request)
    {
        return view('roster.checkin', [
            'roster_id' => $request->query('roster_id'),
            'checksum'  => $request->query('checksum'),
        ]);
    }

    /**
     * POST /rosters/checkin  (AJAX / QR scanner endpoint)
     */
    public function checkin(Request $request): JsonResponse
    {
        $rosterId = $request->input('roster_id');
        $checksum = $request->input('checksum');

        if (!$rosterId || !$checksum) {
            return response()->json(['message' => 'Invalid payload'], 422);
        }

        $roster = Roster::findOrFail($rosterId);

        if (! $this->packetService->verifyChecksum($roster, $checksum)) {
            return response()->json(['message' => 'Invalid QR code.'], 403);
        }

        if ($roster->isCheckedIn()) {
            return response()->json([
                'success'      => true,
                'status'       => 'checked-in',
                'message'      => 'Already checked-in',
                'event'        => $roster->event->name ?? '',
                'organization' => $roster->organization->name ?? '',
            ]);
        }

        $this->packetService->checkIn($roster);

        return response()->json([
            'success'      => true,
            'status'       => 'checked-in',
            'message'      => 'Successfully checked in',
            'event'        => $roster->event->name ?? '',
            'organization' => $roster->organization->name ?? '',
        ]);
    }

    // =========================================================================
    // PHASE 2 — QR modal viewer
    // =========================================================================

    /**
     * GET /rosters/{roster}/qr  (AJAX)
     */
    public function showQr(Roster $roster): JsonResponse
    {
        if (! $this->packetService->qrExists($roster->id)) {
            return response()->json([
                'message' => 'QR not generated yet. Please generate the packet first.',
            ], 404);
        }

        return response()->json([
            'qr_url'       => $this->packetService->qrUrl($roster->id),
            'organization' => $roster->organization?->name,
            'event'        => $roster->event?->name,
        ]);
    }

    // =========================================================================
    // SAMPLE TEMPLATE DOWNLOAD
    // =========================================================================

    public function download(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $headers = [
            'Name', 'Age', 'Grade', 'Gender', 'player_email',
            'Shirt Size', 'Team', 'Group', 'Pod', 'Subgroup',
            'Division', 'Coach', 'Organization',
        ];

        $sheet->fromArray($headers, null, 'A1');

        $sheet->fromArray([
            'John Doe', 12, 7, 'male', 'john@example.com',
            'M', 'Team A', 'Group 1', 'Red', 'Subgroup A',
            'Primary', 'Coach Smith', 'ABC School',
        ], null, 'A2');

        $writer   = new Xlsx($spreadsheet);
        $fileName = 'roster_sample_template.xlsx';

        return response()->streamDownload(
            fn () => $writer->save('php://output'),
            $fileName
        );
    }

    // =========================================================================
    // GAME CARD EXPORT
    // =========================================================================

    /**
     * GET /rosters/export/game-cards
     */
    public function exportGameCards(Request $request): StreamedResponse
    {
        $query = Roster::with([
            'event',
            'organization',
            'organization.coach',
            'students.team.group',
            'students.team.subGroup',
        ]);

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->integer('event_id'));
        }

        $rosters = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Game Cards');

        // ── Headers ────────────────────────────────────────────────────────
        // NOTE: GuardianPassword column removed — never export hashed passwords.
        $headers = [
            'FirstName', 'LastName', 'Gender', 'Birthday', 'Religion',
            'BloodGroup', 'Caste', 'CategoryID', 'Roll', 'RegisterNo',
            'AdmissionDate', 'GuardianName', 'GuardianRelation',
            'GuardianMobileNo', 'GuardianEmail', 'GuardianUsername',
        ];

        $sheet->fromArray($headers, null, 'A1');

        // ── Header styling ─────────────────────────────────────────────────
        $headerStyle = [
            'font' => [
                'bold'  => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'name'  => 'Arial',
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1F4E78'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFB8CCE4'],
                ],
            ],
        ];

        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(20);

        // ── Data rows ──────────────────────────────────────────────────────
        $rowIndex = 2;

        foreach ($rosters as $roster) {
            foreach ($roster->students as $student) {
                $nameParts = explode(' ', trim($student->name ?? ''), 2);
                $firstName = $nameParts[0] ?? '';
                $lastName  = $nameParts[1] ?? '';

                $team     = $student->team;
                $group    = $team?->group;
                $subGroup = $team?->subGroup;
                $org      = $roster->organization;
                $coach    = $org?->coach;
                $event    = $roster->event;

                $row = [
                    $firstName,
                    $lastName,
                    $student->gender ?? '',
                    $student->dob
                        ? \Carbon\Carbon::parse($student->dob)->format('Y-m-d')
                        : '',
                    $group?->group_name ?? '',      // Religion  → group name
                    $team?->name ?? '',             // BloodGroup → team name
                    $org?->name ?? '',              // Caste      → org name
                    $subGroup?->name ?? '',         // CategoryID → subgroup name
                    $student->id ?? '',             // Roll       → student PK
                    $event?->id ?? '',              // RegisterNo → event id
                    $roster->created_at
                        ? $roster->created_at->format('Y-m-d')
                        : '',
                    $coach?->name ?? '',            // GuardianName  → coach name
                    'Coach',                        // GuardianRelation
                    '',                             // GuardianMobileNo (not stored)
                    $student->email ?? '',          // GuardianEmail → student email
                    $coach?->email ?? '',           // GuardianUsername → coach email
                    // ⚠️  GuardianPassword intentionally removed — NEVER export passwords
                ];

                $sheet->fromArray($row, null, "A{$rowIndex}");

                // Alternating row fill
                if ($rowIndex % 2 === 0) {
                    $sheet->getStyle("A{$rowIndex}:{$lastCol}{$rowIndex}")
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFD9E1F2');
                }

                // Row borders
                $sheet->getStyle("A{$rowIndex}:{$lastCol}{$rowIndex}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()->setARGB('FFB8CCE4');

                $sheet->getStyle("A{$rowIndex}:{$lastCol}{$rowIndex}")
                    ->getFont()
                    ->setName('Arial')
                    ->setSize(10);

                $rowIndex++;
            }
        }

        // ── Auto-size columns ──────────────────────────────────────────────
        foreach (range(1, count($headers)) as $colIdx) {
            $sheet->getColumnDimensionByColumn($colIdx)->setAutoSize(true);
        }

        // ── Freeze pane & auto-filter ──────────────────────────────────────
        $sheet->freezePane('A2');
        $sheet->setAutoFilter("A1:{$lastCol}1");

        // ── Stream response ────────────────────────────────────────────────
        $eventSuffix = $request->filled('event_id')
            ? '_event' . $request->integer('event_id')
            : '_all';

        $fileName = 'game_cards' . $eventSuffix . '_' . now()->format('Ymd_His') . '.xlsx';
        $writer   = new Xlsx($spreadsheet);

        return response()->streamDownload(
            fn () => $writer->save('php://output'),
            $fileName,
            [
                'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control'       => 'max-age=0',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            ]
        );
    }
}