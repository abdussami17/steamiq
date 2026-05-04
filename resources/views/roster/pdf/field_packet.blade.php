<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Field Packet — {{ $roster->organization?->name }}</title>
<style>
    /* ─────────────────────────────────────────────────────────────────
       DOMPDF NOTES:
       - No flexbox / grid (not supported)
       - No CSS variables (not supported)
       - Use table-based layout for multi-column rows
       - Fonts: DejaVu Sans is the safest built-in; loaded below
       - @page margin controls printable area exactly
    ───────────────────────────────────────────────────────────────── */

    @page {
        margin: 12mm 10mm 10mm 10mm;
        size: A4 landscape;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 9pt;
        color: #1a1a1a;
        background: #ffffff;
        width: 100%;
    }

    /* ── Header ─────────────────────────────────────────────────────── */
    .header-table {
        width: 100%;
        border-bottom: 2.5pt solid #1a1a1a;
        margin-bottom: 8pt;
        padding-bottom: 6pt;
    }

    .header-table td {
        vertical-align: top;
        padding: 0;
    }

    .org-name {
        font-size: 18pt;
        font-weight: bold;
        line-height: 1.1;
        color: #0f2a4a;
        letter-spacing: -0.3pt;
    }

    .event-name {
        font-size: 9.5pt;
        color: #555555;
        margin-top: 2pt;
    }

    .header-right {
        text-align: right;
        font-size: 8.5pt;
        color: #555555;
        line-height: 1.8;
    }

    .badge-status {
        background: #1d4ed8;
        color: #ffffff;
        font-size: 7.5pt;
        font-weight: bold;
        padding: 2pt 7pt;
        border-radius: 2pt;
        letter-spacing: 0.5pt;
        text-transform: uppercase;
    }

    /* ── Meta bar ───────────────────────────────────────────────────── */
    .meta-table {
        width: 100%;
        background: #f0f2f5;
        border: 0.5pt solid #d8dce2;
        border-radius: 3pt;
        margin-bottom: 10pt;
        padding: 5pt 8pt;
    }

    .meta-table td {
        font-size: 9pt;
        padding: 0 12pt 0 0;
        white-space: nowrap;
        vertical-align: middle;
    }

    .meta-table td:last-child {
        padding-right: 0;
    }

    .meta-label {
        font-weight: bold;
        color: #333333;
    }

    /* ── Team section ────────────────────────────────────────────────── */
    .team-section {
        margin-bottom: 14pt;
        page-break-inside: avoid;
    }

    .team-heading {
        width: 100%;
        background: #0f2a4a;
        color: #ffffff;
        font-size: 10.5pt;
        font-weight: bold;
        padding: 4pt 8pt;
        border-radius: 2pt 2pt 0 0;
        letter-spacing: 0.2pt;
    }

    .team-heading-table {
        width: 100%;
        background: #0f2a4a;
        color: #ffffff;
        border-radius: 2pt 2pt 0 0;
    }

    .team-heading-table td {
        padding: 4pt 8pt;
        font-size: 10.5pt;
        font-weight: bold;
        color: #ffffff;
        vertical-align: middle;
    }

    .team-count-cell {
        text-align: right;
        font-size: 8.5pt;
        font-weight: normal;
        opacity: 0.85;
    }

    /* ── Data Table ──────────────────────────────────────────────────── */
    .roster-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 8.5pt;
    }

    .roster-table thead tr {
        background: #dde4ef;
    }

    .roster-table thead th {
        padding: 3.5pt 5pt;
        text-align: left;
        font-weight: bold;
        font-size: 8.5pt;
        border: 0.5pt solid #adb8c9;
        color: #1a2a3a;
        white-space: nowrap;
    }

    .roster-table thead th.center {
        text-align: center;
    }

    .roster-table tbody tr.even {
        background: #f7f9fc;
    }

    .roster-table tbody tr.odd {
        background: #ffffff;
    }

    .roster-table tbody td {
        padding: 3pt 5pt;
        border: 0.5pt solid #d0d6df;
        vertical-align: middle;
        line-height: 1.35;
    }

    .roster-table tbody td.center {
        text-align: center;
    }

    .student-name {
        font-weight: bold;
        color: #0f2a4a;
    }

    .idx-cell {
        text-align: center;
        color: #888888;
        font-size: 8pt;
    }

    /* Checkbox */
    .check-box {
        display: inline-block;
        width: 13pt;
        height: 13pt;
        border: 1.5pt solid #666666;
        border-radius: 1.5pt;
    }

    /* Shirt size badge */
    .shirt-badge {
        background: #e8f0fe;
        color: #1d4ed8;
        font-weight: bold;
        font-size: 8pt;
        padding: 1pt 4pt;
        border-radius: 2pt;
        border: 0.5pt solid #bfcfee;
    }

    /* ── Footer ──────────────────────────────────────────────────────── */
    .footer-table {
        width: 100%;
        border-top: 0.75pt solid #cccccc;
        margin-top: 12pt;
        padding-top: 5pt;
    }

    .footer-table td {
        font-size: 7.5pt;
        color: #999999;
        vertical-align: top;
        padding: 0;
    }

    .footer-table td.right {
        text-align: right;
    }
</style>
</head>
<body>

    {{-- ── Header ──────────────────────────────────────────────────────── --}}
    <table class="header-table" cellspacing="0" cellpadding="0">
        <tr>
            <td style="width:65%">
                <div class="org-name">{{ $roster->organization?->name ?? 'Organization' }}</div>
                <div class="event-name">{{ $roster->event?->name ?? 'Event' }}</div>
            </td>
            <td style="width:35%" class="header-right">
                <span class="badge-status">{{ strtoupper($roster->status) }}</span><br/>
                Roster #{{ $roster->id }}<br/>
                Generated: {{ $generatedAt }}
            </td>
        </tr>
    </table>

    {{-- ── Meta row ─────────────────────────────────────────────────────── --}}
    <table class="meta-table" cellspacing="0" cellpadding="0">
        <tr>
            <td><span class="meta-label">Coach:</span> {{ $roster->organization?->coach?->name ?? '—' }}</td>
            <td><span class="meta-label">Total Players:</span> {{ $roster->students->count() }}</td>
            <td><span class="meta-label">Teams:</span> {{ count($grouped) }}</td>
            <td><span class="meta-label">Event:</span> {{ $roster->event?->name ?? '—' }}</td>
        </tr>
    </table>

    {{-- ── Student table grouped by team ──────────────────────────────── --}}
    @php $globalIndex = 1; @endphp

    @foreach($grouped as $teamName => $students)
    <div class="team-section">

        {{-- Team heading --}}
        <table class="team-heading-table" cellspacing="0" cellpadding="0">
            <tr>
                <td>{{ $teamName }}</td>
                <td class="team-count-cell">
                    {{ count($students) }} player{{ count($students) !== 1 ? 's' : '' }}
                </td>
            </tr>
        </table>

        {{-- Students --}}
        <table class="roster-table" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th style="width:4%"  class="center">#</th>
                    <th style="width:26%">Student Name</th>
                    <th style="width:7%"  class="center">Age</th>
                    <th style="width:9%"  class="center">Grade</th>
                    <th style="width:30%">Team</th>
                    <th style="width:12%" class="center">Shirt</th>
                    <th style="width:12%" class="center">Check ✓</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $i => $student)
                <tr class="{{ $i % 2 === 0 ? 'odd' : 'even' }}">
                    <td class="idx-cell center">{{ $globalIndex++ }}</td>
                    <td><span class="student-name">{{ $student->name }}</span></td>
                    <td class="center">{{ $student->age ?? '—' }}</td>
                    <td class="center">{{ $student->grade ?? '—' }}</td>
                    <td>{{ $student->team?->name ?? '—' }}</td>
                    <td class="center">
                        @if($student->shirt_size)
                            <span class="shirt-badge">{{ strtoupper($student->shirt_size) }}</span>
                        @else
                            —
                        @endif
                    </td>
                    <td class="center"><span class="check-box"></span></td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
    @endforeach

    {{-- ── Footer ──────────────────────────────────────────────────────── --}}
    <table class="footer-table" cellspacing="0" cellpadding="0">
        <tr>
            <td>Steamiq Field Packet &mdash; Roster #{{ $roster->id }} &mdash; {{ $roster->organization?->name }}</td>
            <td class="right">{{ $generatedAt }}</td>
        </tr>
    </table>

</body>
</html>