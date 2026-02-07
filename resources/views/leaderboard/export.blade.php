<table>
    <thead>
        <tr>
            <th>Rank</th>
            <th>Team / Player</th>
            <th>Brain</th>
            <th>Play</th>
            <th>E-Game</th>
            <th>Esports</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($teams as $team)
            <tr>
                <td>{{ $team->rank ?? 'N/A' }}</td>
                <td>{{ $team->team_name ?? 'N/A' }}</td>
                <td>{{ $team->brain ?? 0 }}</td>
                <td>{{ $team->play ?? 0 }}</td>
                <td>{{ $team->egame ?? 0 }}</td>
                <td>{{ $team->esports ?? 0 }}</td>
                <td>{{ $team->total ?? 0 }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
