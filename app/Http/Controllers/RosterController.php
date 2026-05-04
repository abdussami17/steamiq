<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Roster;
use App\Services\RosterImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\RosterPacketService;
use Illuminate\Support\Facades\Gate;

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
     * Render the roster management page with event dropdown data.
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
     * Returns the current roster table rows as JSON.
     * Optionally filtered by ?event_id=
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
            'has_pdf' => app(RosterPacketService::class)->pdfExists($r->id),
            'has_qr'  => app(RosterPacketService::class)->qrExists($r->id),
            'pdf_url' => app(RosterPacketService::class)->pdfUrl($r->id),
            'qr_url'  => app(RosterPacketService::class)->qrUrl($r->id),
        ]);

        return response()->json(['data' => $rosters]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // IMPORT
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * POST /rosters/import  (AJAX)
     *
     * Expected form fields:
     *   - event_id  (integer, required)
     *   - file      (file, required, mimes: xlsx,csv, max: 10MB)
     */
    public function import(Request $request): JsonResponse
    {
        // ── Authorisation ────────────────────────────────────────────────────
        // Adjust permission name to match your Spatie setup
        if (! $request->user()->hasAnyRole(['admin', 'coach'])) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        // ── Validation ───────────────────────────────────────────────────────
        $validated = $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'file'     => [
                'required',
                'file',
                'mimes:xlsx,csv',
                'max:10240', // 10 MB
            ],
        ]);

        // ── Import ────────────────────────────────────────────────────────────
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
    public function show(Roster $roster)
    {
        $roster->load([
            'event',
            'organization.coach',
            'students.team.group',
        ]);
    
        return response()->json([
            'id' => $roster->id,
            'event' => $roster->event->name ?? '—',
            'organization' => $roster->organization->name ?? '—',
            'coach' => $roster->organization->coach->name ?? '—',
            'status' => $roster->status,
            'players' => $roster->students->map(function ($s) {
                return [
                    'name'  => $s->name,
                    'age'   => $s->age,
                    'grade' => $s->grade,
                    'team'  => $s->team->name ?? '—',
                    'group' => $s->team->group->group_name ?? '—',
                ];
            }),
        ]);
    }


    
    // =========================================================================
    // PHASE 2 — Field Packet Generation
    // =========================================================================
 
    /**
     * POST /rosters/{roster}/generate-packet  (AJAX)
     * Generates PDF + QR, transitions roster status: draft → ready
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
    // PHASE 2 — QR Check-In
    // =========================================================================
 
    /**
     * POST /rosters/checkin  (AJAX / QR scanner endpoint)
     * Input JSON: { "roster_id": X, "checksum": "..." }
     */
    public function checkin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'roster_id' => ['required', 'integer', 'exists:rosters,id'],
            'checksum'  => ['required', 'string'],
        ]);
 
        $roster = Roster::findOrFail($validated['roster_id']);
 
        if (! $this->packetService->verifyChecksum($roster, $validated['checksum'])) {
            return response()->json(['message' => 'Invalid QR code.'], 403);
        }
 
        if ($roster->isCheckedIn()) {
            return response()->json([
                'success' => true,
                'message' => 'Already checked in.',
                'status'  => 'checked-in',
            ]);
        }
 
        try {
            $this->packetService->checkIn($roster);
 
            return response()->json([
                'success'      => true,
                'message'      => 'Check-in complete.',
                'status'       => 'checked-in',
                'organization' => $roster->organization?->name,
                'event'        => $roster->event?->name,
            ]);
 
        } catch (\Throwable $e) {
            \Log::error("Check-in failed for roster #{$roster->id}", ['exception' => $e->getMessage()]);
            return response()->json(['message' => 'Check-in failed.'], 500);
        }
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
            return response()->json(['message' => 'QR not generated yet. Please generate the packet first.'], 404);
        }
 
        return response()->json([
            'qr_url'       => $this->packetService->qrUrl($roster->id),
            'organization' => $roster->organization?->name,
            'event'        => $roster->event?->name,
        ]);
    }
}