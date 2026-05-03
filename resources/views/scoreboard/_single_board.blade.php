{{--
    resources/views/scoreboard/_single_board.blade.php
    Renders ONE division scoreboard (Primary OR Junior).
    Props: $eventName, $division, $rows (array), $activities (Collection)
--}}

@php
    $divUp     = strtoupper($division);
    $ageStr    = match($division) {
        'Junior'  => '11–14 YRS',
        'Primary' => '7–10 YRS',
        default   => '',
    };
    $totalTeams = count($rows);
    $totalCols  = 8; // team no, team name, members, total points, division, flags, org, rank

    // Group rows by group_id, preserving the rank-sorted order
    $grouped = collect($rows)->groupBy('group_id');
@endphp

<div class="pg-board">

    {{-- Title bar --}}
    <div class="pg-titlebar">
        <div class="pg-board-name">{{ Str::upper($eventName) }} &mdash; {{ $divUp }}</div>
        @if($ageStr)
            <div class="pg-board-badge">{{ $ageStr }}</div>
        @endif
    </div>

    {{-- Team count --}}
    <div class="pg-count">{{ $totalTeams }} TEAM{{ $totalTeams !== 1 ? 'S' : '' }}</div>

    {{-- Table --}}
    <div class="pg-scroll">
    <table class="pg-table">
        <thead>
            <tr>
                <th>Team No.</th>
                <th class="th-l">Team Name</th>
                <th class="th-l">Members</th>
                <th>Total Points</th>
                <th>Division</th>
                <th>Flags</th>
                <th class="th-l">ORG</th>
                <th class="th-rank">Rank</th>
            </tr>
        </thead>
        <tbody>

            @if($totalTeams === 0)
                <tr>
                    <td colspan="{{ $totalCols }}" class="pg-empty">
                        No {{ $division }} teams found for this event.
                    </td>
                </tr>
            @else
                @php $teamCounter = 0; @endphp
                @foreach($grouped as $groupId => $groupRows)
                    {{-- Group header --}}
                    <tr class="pg-grp">
                        <td colspan="{{ $totalCols }}">
                            {{ $groupRows->first()['group_name'] }}
                        </td>
                    </tr>

                    {{-- Data rows (already rank-sorted) --}}
                    @foreach($groupRows as $row)
                        @php
                            $teamCounter++;
                            $rkCls = match($row['rank']) { 1 => 'r1', 2 => 'r2', 3 => 'r3', default => '' };
                        @endphp
                        <tr class="pg-dr">
                            <td class="td-no">{{ $teamCounter }}</td>
                            <td class="td-name">{{ $row['team_name'] }}</td>
                            <td class="td-mem">{{ $row['members'] ?: '—' }}</td>
                            <td class="td-pts">{{ number_format($row['total_points'] ?? 0) }}</td>
                            <td class="td-div">{{ Str::upper($row['division'] ?? '') }}</td>

                            <td class="td-flg">
                                {{ $row['flag_totals'] ?? 0 }}
                                @if(!empty($row['cards']))
                                    @php
                                        $cardCounts = [];
                                        foreach ($row['cards'] as $c) {
                                            $t = $c['type'] ?? 'unknown';
                                            $cardCounts[$t] = ($cardCounts[$t] ?? 0) + 1;
                                        }
                                    @endphp
                                    <span class="card-badges" style="margin-left:8px;">
                                        @foreach($cardCounts as $type => $count)
                                            @php
                                                $cls = $type === 'red' ? 'card-red' : ($type === 'yellow' ? 'card-yellow' : ($type === 'orange' ? 'card-orange' : 'card-unknown'));
                                            @endphp
                                            <span title="{{ strtoupper($type) }} card" class="card-badge {{ $cls }}">{{ $count }}</span>
                                        @endforeach
                                    </span>
                                @endif
                            </td>
                            <td class="td-org">{{ $row['org_name'] }}</td>
                            <td class="td-rank">
                                <span class="rk-pill {{ $rkCls }}">{{ $row['rank'] }}</span>
                            </td>
                        </tr>
                    @endforeach

                @endforeach
            @endif

        </tbody>
    </table>
    </div>{{-- .pg-scroll --}}

</div>{{-- .pg-board --}}