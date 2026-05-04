<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Roster;
use App\Services\RosterImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RosterController extends Controller
{
    public function __construct(private RosterImportService $importService)
    {
       
    }

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
}