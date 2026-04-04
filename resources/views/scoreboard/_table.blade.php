{{--
    resources/views/scoreboard/_table.blade.php
    Props:
        $eventName   – string  e.g. "2025 OCEAN BOWL GAMES"
        $division    – string  "Primary" | "Junior"
        $rows        – array   from ScoreboardController::buildDivisionData()
        $activities  – Collection of ChallengeActivity
--}}

@php
    $divisionLabel = strtoupper($division);
    // Build age-range label (matches Excel header style)
    $ageLabel = match($division) {
        'Junior'  => '(11 TO 14 YRS. OLD)',
        'Primary' => '(7 TO 10 YRS. OLD)',
        default   => '',
    };
    $totalTeams    = count($rows);
    $actCount      = $activities->count();
    // total colspan = team_no + name + members + division + activities + points + flags + org + rank
    $totalCols     = 5 + $actCount + 3 + 1; // +1 for rank (last column)

    // Group rows by group_id for rendering group headers
    $grouped = collect($rows)->groupBy('group_id');
@endphp

<div class="sb-division-block mb-5">

    {{-- ── Yellow title bar ──────────────────────────────────────── --}}
    <table class="sb-excel-table">
        <tbody>
            <tr>
                <td colspan="{{ $totalCols }}" class="cell-title">
                    {{ strtoupper($eventName) }} - {{ $divisionLabel }}
                    @if($ageLabel) {{ $ageLabel }} @endif
                </td>
            </tr>
            <tr>
                <td colspan="{{ $totalCols }}" class="cell-team-count">
                    {{ $totalTeams }} TEAM{{ $totalTeams !== 1 ? 'S' : '' }}
                </td>
            </tr>

            @if($totalTeams === 0)
                <tr>
                    <td colspan="{{ $totalCols }}" class="cell-empty">
                        No {{ $division }} teams found for this event.
                    </td>
                </tr>
            @else
                @foreach($grouped as $groupId => $groupRows)
                    @php $groupName = $groupRows->first()['group_name']; @endphp

                    {{-- ── Group column header row ────────────────────────── --}}
                    <tr class="row-group-header">
                        <td class="cell-gh cell-gh-no">
                            {{ strtoupper($groupName) }}<br>TEAM NO:
                        </td>
                        <td class="cell-gh">TEAM NAME</td>
                        <td class="cell-gh">TEAM<br>MEMBERS</td>
                        <td class="cell-gh">DIVISION</td>
                        @foreach($activities as $act)
                            <td class="cell-gh cell-points-hdr">
                                Automatic<br>{{ Str::upper($act->display_name) }}
                            </td>
                        @endforeach
                        @if($actCount === 0)
                            <td class="cell-gh cell-points-hdr">Automatic<br>YOUR POINTS</td>
                        @endif
                        <td class="cell-gh">Flag Totals</td>
                        <td class="cell-gh">ORG</td>
                        <td class="cell-gh cell-rank-hdr">RANK</td>
                    </tr>

                    {{-- ── Data rows ───────────────────────────────────────── --}}
                    @foreach($groupRows as $row)
                        @php
                            $rankBg = match($row['rank']) {
                                1 => 'rank-gold',
                                2 => 'rank-silver',
                                3 => 'rank-bronze',
                                default => 'rank-normal',
                            };
                        @endphp
                        <tr class="row-data">
                            <td class="cell-d cell-team-no">{{ $row['team_no'] }}</td>
                            <td class="cell-d cell-team-name">{{ $row['team_name'] }}</td>
                            <td class="cell-d cell-members">{{ $row['members'] ?: '' }}</td>
                            <td class="cell-d cell-division">{{ strtoupper($row['division']) }}</td>

                            @foreach($activities as $act)
                                @php
                                    $pts = $row['activity_scores'][$act->id] ?? 0;
                                @endphp
                                <td class="cell-d cell-pts {{ $pts > 0 ? 'pts-has' : 'pts-zero' }}">
                                    {{ $pts > 0 ? number_format($pts) : '' }}
                                </td>
                            @endforeach
                            @if($actCount === 0)
                                <td class="cell-d cell-pts">{{ number_format($row['total_points']) }}</td>
                            @endif

                            <td class="cell-d cell-flags">{{ $row['flag_totals'] ?: '0' }}</td>
                            <td class="cell-d cell-org">{{ $row['org_name'] }}</td>
                            <td class="cell-rank {{ $rankBg }}">{{ $row['rank'] }}</td>
                        </tr>
                    @endforeach

                @endforeach
            @endif
        </tbody>
    </table>

</div>