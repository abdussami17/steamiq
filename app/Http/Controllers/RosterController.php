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
        
        // Selected roster export
        if ($request->filled('roster_ids')) {
            $query->whereIn('id', $request->roster_ids);
        }
        $rosters = $query->get();
    
        // CSV Headers
        $headers = [
            'FirstName',
            'LastName',
            'Gender',
            'Birthday',
            'Religion',
            'BloodGroup',
            'Caste',
            'CategoryID',
            'Roll',
            'RegisterNo',
            'AdmissionDate',
            'GuardianName',
            'GuardianRelation',
            'GuardianMobileNo',
            'GuardianEmail',
            'GuardianUsername',
        ];
    
        $eventSuffix = $request->filled('event_id')
            ? '_event' . $request->integer('event_id')
            : '_all';
    
        $fileName = 'game_cards' . $eventSuffix . '_' . now()->format('Ymd_His') . '.csv';
    
        return response()->streamDownload(function () use ($rosters, $headers) {
    
            $handle = fopen('php://output', 'w');
    
            // UTF-8 BOM for Excel support
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
    
            // Header row
            $paddedHeaders = array_map(function ($header) {
                return str_pad($header, 25, ' ');
            }, $headers);
            
            fputcsv($handle, $paddedHeaders);
    
            // Data rows
            foreach ($rosters as $roster) {
    
                foreach ($roster->students as $student) {
    
                    $nameParts = explode(' ', trim($student->name ?? ''), 2);
    
                    $firstName = $nameParts[0] ?? '';
                    $lastName  = $nameParts[1] ?? '';
    
                    $team  = $student->team;
$group = $team?->group;

$subGroupName = $team && $team->subGroup
    ? trim((string) $team->subGroup->name)
    : '';

$org   = $roster->organization;
$coach = $org?->coach;
$event = $roster->event;

$row = [
    $firstName,
    $lastName,
    $student->gender ?? '',
    $student->dob
        ? \Carbon\Carbon::parse($student->dob)->format('Y-m-d')
        : '',
    $group?->group_name ?? '',
    $team?->name ?? '',
    $org?->name ?? '',
    $subGroupName,
    $student->id ?? '',
    $event?->id ?? '',
    $roster->created_at
        ? $roster->created_at->format('F d, Y')
        : '',
    $coach?->name ?? '',
    'Coach',
    '',
    $student->email ?? '',
    $coach?->email ?? '',
];
    
                    fputcsv($handle, $row);
                }
            }
    
            fclose($handle);
    
        }, $fileName, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Cache-Control'       => 'no-store, no-cache',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }
}