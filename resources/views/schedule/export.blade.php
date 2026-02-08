<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Match Name</th>
            <th>Team A</th>
            <th>Team B</th>
            <th>Status</th>
            <th>Date</th>
            <th>Time</th>
            <th>PIN</th>
        </tr>
    </thead>
    <tbody>
        @foreach($matches as $index => $match)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $match->match_name ?? 'N/A' }}</td>
                <td>{{ $match->teamA->team_name ?? 'TBD' }}</td>
                <td>{{ $match->teamB->team_name ?? 'TBD' }}</td>
                <td>{{ strtoupper($match->status ?? 'N/A') }}</td>
                <td>{{ $match->date ?? 'N/A' }}</td>
                <td>{{ $match->time ?? 'N/A' }}</td>
                <td>{{ $match->pin ?? '—' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
