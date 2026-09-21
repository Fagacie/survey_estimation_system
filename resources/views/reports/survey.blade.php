<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Survey Report - {{ $project->name }}</title>
    <style>
        @page { margin: 16mm; }
        @media screen {
            body { padding: 16mm !important; margin: 0; box-sizing: border-box; }
        }
        body { 
            font-family: DejaVu Sans, sans-serif; 
            color: #1f2937; 
            font-size: 10px; 
            margin: 0;
            box-sizing: border-box;
        }
        h1 { color: #174a73; font-size: 22px; margin: 0 0 4px; }
        h2 { color: #174a73; font-size: 14px; border-bottom: 1px solid #cbd5e1; padding-bottom: 5px; margin-top: 22px; }
        .muted { color: #64748b; }
        .meta, .data { width: 100%; border-collapse: collapse; margin-top: 8px; table-layout: fixed; }
        .meta td, .data td, .data th { border: 1px solid #cbd5e1; padding: 6px; overflow: hidden; word-wrap: break-word; }
        .data th { background: #174a73; color: white; text-align: left; }
        .label { font-weight: bold; background: #f1f5f9; width: 25%; }
        .summary { width: 100%; table-layout: fixed; }
        .summary td { width: 25%; padding: 8px; border: 1px solid #cbd5e1; text-align: center; }
        .value { display: block; color: #174a73; font-size: 15px; font-weight: bold; }
        .area { margin-top: 12px; }
        .area-image { margin-top: 8px; text-align: center; }
        img { width: 650px; border: 1px solid #94a3b8; }
    </style>
</head>
<body>
    <h1>HYDROGRAPHIC SURVEY REPORT</h1>
    <div class="muted">Generated {{ $generated_at->format('d M Y H:i') }}</div>

    <table class="meta">
        <tr><td class="label">Project</td><td>{{ $project->name }}</td><td class="label">Project No.</td><td>{{ $project->number ?? 'N/A' }}</td></tr>
        <tr><td class="label">Client</td><td>{{ $project->client?->company_name ?? 'N/A' }}</td><td class="label">Period</td><td>{{ $project->period ?? 'N/A' }}</td></tr>
    </table>

    <h2>Survey Summary</h2>
    <table class="summary">
        <tr>
            <td><span class="value">{{ number_format($duration['distance_nm'], 4) }}</span>Distance (NM)</td>
            <td><span class="value">{{ number_format($duration['survey_hours'], 2) }}</span>Survey Hours</td>
            <td><span class="value">{{ number_format($duration['execution_days'], 2) }}</span>Execution Days</td>
            <td><span class="value">{{ number_format($duration['total_days'], 2) }}</span>Total Duration</td>
        </tr>
    </table>

    <table class="data">
        <tr><td class="label">Weather Standby</td><td>{{ number_format($duration['weather_days'], 2) }} days</td><td class="label">MOB/DEMOB</td><td>{{ number_format($duration['mod_demod_days'], 2) }} days</td></tr>
        <tr><td class="label">Patch Test</td><td>{{ number_format($duration['patch_test_days'], 2) }} days</td><td class="label">Survey Areas</td><td>{{ $locations->count() }}</td></tr>
    </table>

    <h2>Survey Areas</h2>
    @forelse($locations as $location)
        <div class="area">
            <h3>{{ $location['name'] }}</h3>
            <table class="data">
                <tr><td class="label">Distance</td><td>{{ number_format($location['distance_nm'], 4) }} NM</td><td class="label">Survey Hours</td><td>{{ number_format($location['survey_hours'], 2) }}</td></tr>
                <tr><td class="label">Execution Days</td><td>{{ number_format($location['execution_days'], 2) }}</td><td class="label">Survey Lines</td><td>{{ $location['line_count'] }} ({{ $location['main_line_count'] }} main, {{ $location['cross_line_count'] }} cross)</td></tr>
                <tr><td class="label">Boundaries</td><td>{{ $location['boundary_count'] }}</td><td class="label">Boundary Area</td><td>{{ number_format($location['boundary_area'], 2) }} m2</td></tr>
            </table>
            @if($location['screenshot'])
                <div class="area-image">
                    <img src="{{ $location['screenshot'] }}" alt="Survey map for {{ $location['name'] }}">
                </div>
            @endif
        </div>
    @empty
        <p class="muted">No survey areas have been saved.</p>
    @endforelse
</body>
</html>
