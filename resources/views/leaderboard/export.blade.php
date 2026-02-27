<table>
    <thead>
        <tr>
            <th>Rank</th>
            <th>Type</th>
            <th>Organization</th>
            <th>Group</th>
            <th>Subgroup</th>
            <th>Team</th>
            <th>Student</th>

            @foreach($categories as $cat)
                <th>{{ $cat->name }}</th>
            @endforeach

            <th>Total</th>
        </tr>
    </thead>

    <tbody>
        @foreach($rows as $row)
            <tr>
                <td>{{ $row['rank'] }}</td>
                <td>{{ ucfirst($row['type']) }}</td>
                <td>{{ $row['organization'] ?? 'N/A' }}</td>
                <td>{{ $row['group'] ?? 'N/A' }}</td>
                <td>{{ $row['subgroup'] ?? 'N/A' }}</td>
                <td>{{ $row['team_name'] ?? 'N/A' }}</td>
                <td>{{ $row['student_name'] ?? 'N/A' }}</td>

                @foreach($categories as $cat)
                    <td>{{ $row['scores'][$cat->name] ?? 0 }}</td>
                @endforeach

                <td>{{ $row['total'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>