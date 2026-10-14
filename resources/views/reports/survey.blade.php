<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $report_title }} - {{ $project->name }}</title>
    <style>
        @page {
            margin: 12mm 14mm 14mm 14mm;
            size: A4 portrait;
        }
        @media screen {
            body {
                padding: 14mm !important;
                margin: 0 auto;
                max-width: 210mm;
                background: #ffffff;
            }
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: DejaVu Sans, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            color: #1e293b;
            font-size: 9.5px;
            line-height: 1.45;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }
        
        /* Typography */
        h1 {
            color: #0f2d4a;
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 3px 0;
            letter-spacing: -0.01em;
            text-transform: uppercase;
        }
        h2 {
            color: #0f2d4a;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin: 16px 0 8px 0;
            padding-bottom: 4px;
            border-bottom: 1.5px solid #0284c7;
        }
        h3 {
            color: #0f2d4a;
            font-size: 11px;
            font-weight: bold;
            margin: 12px 0 6px 0;
        }
        h4 {
            color: #334155;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin: 10px 0 4px 0;
        }
        .muted { color: #64748b; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        /* Report masthead */
        .doc-header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            background: #102b46;
            border-top: 4px solid #1685b5;
        }
        .doc-header-table td {
            vertical-align: top;
            padding: 13px 14px 12px;
            color: #ffffff;
        }
        .doc-brand {
            color: #b9e6f7;
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .doc-header-table h1 {
            color: #ffffff;
            font-size: 17px;
            line-height: 1.25;
            margin: 0 0 5px;
            letter-spacing: 0.015em;
        }
        .doc-header-subtitle {
            color: #d9e5ef;
            font-size: 9px;
            font-weight: bold;
        }
        .doc-header-meta {
            color: #c6d5e2;
            font-size: 8px;
            line-height: 1.65;
            margin-top: 8px;
        }
        .doc-header-meta strong {
            color: #ffffff;
        }
        .doc-type-cell {
            width: 32%;
            text-align: right;
            border-left: 1px solid #38536c;
        }
        .doc-header-table .badge {
            display: inline-block;
            padding: 4px 7px;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.045em;
            border-radius: 2px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-radius: 3px;
        }
        .badge-sbes {
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }
        .badge-drone {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .badge-mixed {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .badge-pending {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .badge-mapped {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        
        /* Standard Tables */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 8px;
            table-layout: fixed;
            page-break-inside: avoid;
        }
        .table td, .table th {
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            font-size: 9px;
            word-wrap: break-word;
        }
        .table th {
            background: #0f2d4a;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            font-size: 9px;
        }
        .table td.label {
            background: #f8fafc;
            color: #475569;
            font-weight: bold;
            width: 25%;
        }
        
        /* Metric Tiles / Summary Cards */
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0 10px 0;
            table-layout: fixed;
            page-break-inside: avoid;
        }
        .kpi-table td {
            width: 25%;
            padding: 8px 6px;
            border: 1px solid #cbd5e1;
            text-align: center;
            background: #ffffff;
            vertical-align: top;
        }
        .kpi-value {
            display: block;
            color: #0f2d4a;
            font-size: 14px;
            font-weight: bold;
            line-height: 1.2;
            margin-bottom: 2px;
        }
        .kpi-label {
            display: block;
            color: #64748b;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .kpi-unit {
            font-size: 9px;
            font-weight: normal;
            color: #0284c7;
        }
        
        /* Area Card Block */
        .area-container {
            margin-top: 14px;
            margin-bottom: 14px;
            page-break-inside: avoid;
        }
        .area-header-bar {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-left: 4px solid #0284c7;
            padding: 6px 10px;
            margin-bottom: 6px;
        }
        .area-header-bar-drone {
            border-left-color: #059669;
        }
        .area-header-title {
            font-size: 11px;
            font-weight: bold;
            color: #0f2d4a;
            display: inline-block;
        }
        
        /* Map Box */
        .map-box {
            margin-top: 8px;
            text-align: center;
            page-break-inside: avoid;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 6px;
            border-radius: 2px;
        }
        .map-box img {
            max-width: 100%;
            height: auto;
            max-height: 380px;
            display: block;
            margin: 0 auto;
            border: 1px solid #94a3b8;
        }
        .map-caption {
            font-size: 8px;
            color: #64748b;
            margin-top: 4px;
            text-align: center;
            font-style: italic;
        }

        /* Notice / Alert Box */
        .alert-box {
            background: #fffbeb;
            border: 1px solid #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 10px 12px;
            margin: 8px 0;
            color: #92400e;
            font-size: 9px;
            border-radius: 2px;
            page-break-inside: avoid;
        }

        .footer {
            margin-top: 20px;
            padding-top: 6px;
            border-top: 1px solid #e2e8f0;
            font-size: 8px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- 1. DOCUMENT HEADER -->
    <table class="doc-header-table">
        <tr>
            <td style="width: 68%;">
                <div class="doc-brand">Eco Hydrotech Solutions</div>
                <h1>{{ $report_title }}</h1>
                <div class="doc-header-subtitle">
                    {{ $is_single ? 'SURVEY AREA: ' . $report_subtitle : 'PROJECT: ' . $project->name }}
                </div>
                <div class="doc-header-meta">
                    <strong>Reference:</strong> {{ $project->number ?? 'N/A' }}<br>
                    <strong>Generated:</strong> {{ $generated_at->format('d M Y, H:i') }}
                </div>
            </td>
            <td class="doc-type-cell">
                @if($report_type === 'drone')
                    <span class="badge badge-drone">UAV Photogrammetry</span>
                @elseif($report_type === 'sbes')
                    <span class="badge badge-sbes">Hydrographic SBES</span>
                @else
                    <span class="badge badge-mixed">Survey Operations</span>
                @endif
                <div class="doc-header-meta">
                    <strong>Project period</strong><br>
                    {{ $project->period ?? '10' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- 2. PROJECT METADATA -->
    <table class="table">
        <tr>
            <td class="label">Project Name</td>
            <td><strong>{{ $project->name }}</strong></td>
            <td class="label">Project Number</td>
            <td><strong>{{ $project->number ?? 'N/A' }}</strong></td>
        </tr>
        <tr>
            <td class="label">Client Organization</td>
            <td>{{ $project->client?->company_name ?? 'N/A' }}</td>
            <td class="label">Project Location</td>
            <td>{{ $project->location ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Survey Scope</td>
            <td>{{ $is_single ? 'Individual Area Assessment' : 'Comprehensive Project Scope (' . $locations->count() . ' Areas)' }}</td>
            <td class="label">Report Classification</td>
            <td>Technical Survey Operations Estimation</td>
        </tr>
    </table>

    <!-- 3. SCOPE-SPECIFIC EXECUTIVE KPI SUMMARY -->
    @if($report_type === 'sbes' || ($report_type === 'mixed' && $summary['sbes']['count'] > 0))
        <h2>Hydrographic Survey Summary (SBES)</h2>
        <table class="kpi-table">
            <tr>
                <td>
                    <span class="kpi-value">{{ number_format($summary['sbes']['total_distance_nm'], 4) }} <span class="kpi-unit">NM</span></span>
                    <span class="kpi-label">Total Survey Distance</span>
                </td>
                <td>
                    <span class="kpi-value">{{ number_format($summary['sbes']['total_hours'], 2) }} <span class="kpi-unit">hrs</span></span>
                    <span class="kpi-label">Estimated Survey Hours</span>
                </td>
                <td>
                    <span class="kpi-value">{{ number_format($summary['sbes']['execution_days'], 2) }} <span class="kpi-unit">days</span></span>
                    <span class="kpi-label">Execution Duration</span>
                </td>
                <td>
                    <span class="kpi-value">{{ number_format($summary['sbes']['total_days'], 2) }} <span class="kpi-unit">days</span></span>
                    <span class="kpi-label">Total Duration (w/ Allowances)</span>
                </td>
            </tr>
        </table>

        <table class="table">
            <tr>
                <td class="label">Weather Standby Allowance</td>
                <td>{{ number_format($summary['sbes']['weather_days'], 2) }} days</td>
                <td class="label">MOB / DEMOB Allowance</td>
                <td>{{ number_format($summary['sbes']['mod_demod_days'], 2) }} days</td>
            </tr>
            <tr>
                <td class="label">Patch Test Allowance</td>
                <td>{{ number_format($summary['sbes']['patch_test_days'], 2) }} days</td>
                <td class="label">Hydrographic Areas Included</td>
                <td>{{ $summary['sbes']['count'] }} Area(s)</td>
            </tr>
        </table>
    @endif

    @if($report_type === 'drone' || ($report_type === 'mixed' && $summary['drone']['count'] > 0))
        <h2>Drone Mapping Survey Summary (UAV Photogrammetry)</h2>
        <table class="kpi-table">
            <tr>
                <td>
                    <span class="kpi-value">{{ number_format($summary['drone']['total_distance_m'], 0) }} <span class="kpi-unit">m</span></span>
                    <span class="kpi-label">Flight Distance ({{ number_format($summary['drone']['total_distance_km'], 2) }} km)</span>
                </td>
                <td>
                    <span class="kpi-value">{{ number_format($summary['drone']['total_images']) }} <span class="kpi-unit">photos</span></span>
                    <span class="kpi-label">Total Exposures</span>
                </td>
                <td>
                    <span class="kpi-value">{{ number_format($summary['drone']['total_sorties']) }} <span class="kpi-unit">sorties</span></span>
                    <span class="kpi-label">Estimated Flights</span>
                </td>
                <td>
                    <span class="kpi-value">{{ number_format($summary['drone']['total_area_ha'], 2) }} <span class="kpi-unit">ha</span></span>
                    <span class="kpi-label">Survey Area ({{ number_format($summary['drone']['total_area_m2'], 2) }} m&sup2;)</span>
                </td>
            </tr>
        </table>
    @endif

    <!-- 4. DETAILED SURVEY AREAS -->
    <h2>Survey Areas & Technical Specifications</h2>

    @forelse($locations as $index => $location)
        <div class="area-container">
            <div class="area-header-bar {{ $location['is_drone'] ? 'area-header-bar-drone' : '' }}">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="vertical-align: middle;">
                            <span class="area-header-title">{{ $index + 1 }}. {{ $location['name'] }}</span>
                            @if($location['is_drone'])
                                <span class="badge badge-drone" style="margin-left: 6px;">Drone UAV</span>
                            @else
                                <span class="badge badge-sbes" style="margin-left: 6px;">Single Beam SBES</span>
                            @endif
                        </td>
                        <td style="text-align: right; vertical-align: middle;">
                            @if($location['is_mapped'])
                                <span class="badge badge-mapped">Status: Mapped & Planned</span>
                            @else
                                <span class="badge badge-pending">Status: Planning Pending</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            @if(!$location['is_mapped'])
                <div class="alert-box">
                    <strong>Planning Notice:</strong> Survey line planning and boundary mapping for <em>"{{ $location['name'] }}"</em> are currently pending. 
                    Technical parameters and flight tracklines will become available once mapped in the survey planning module.
                </div>
            @else
                @if($location['is_drone'])
                    <!-- DRONE SPECIFICATIONS -->
                    <h4>Equipment Platform & Sensor</h4>
                    <table class="table">
                        <tr>
                            <td class="label">UAV Drone Model</td>
                            <td>{{ $location['drone_model'] }}</td>
                            <td class="label">Selected Camera / Sensor</td>
                            <td>{{ $location['camera_model'] }}</td>
                        </tr>
                    </table>

                    <h4>Flight & Photogrammetric Parameters</h4>
                    <table class="table">
                        <tr>
                            <td class="label">Flight Altitude</td>
                            <td>{{ number_format($location['altitude_m'], 2) }} m AGL</td>
                            <td class="label">Ground Sampling Distance (GSD)</td>
                            <td>{{ number_format($location['gsd_cm'], 2) }} cm/px</td>
                        </tr>
                        <tr>
                            <td class="label">Forward Overlap</td>
                            <td>{{ number_format($location['front_overlap'], 1) }} %</td>
                            <td class="label">Side Overlap</td>
                            <td>{{ number_format($location['side_overlap'], 1) }} %</td>
                        </tr>
                        <tr>
                            <td class="label">Flight Speed</td>
                            <td>{{ number_format($location['speed_ms'], 1) }} m/s ({{ number_format($location['speed_ms'] * 3.6, 1) }} km/h)</td>
                            <td class="label">Course Heading / Angle</td>
                            <td>{{ number_format($location['course_angle'], 1) }} &deg;</td>
                        </tr>
                        <tr>
                            <td class="label">Photo Trigger Interval</td>
                            <td>{{ $location['photo_interval_s'] > 0 ? number_format($location['photo_interval_s'], 2) . ' s' : 'Distance based' }}</td>
                            <td class="label">Photo Baseline Spacing</td>
                            <td>{{ number_format($location['photo_spacing_m'], 1) }} m</td>
                        </tr>
                    </table>

                    <h4>Mission Execution Estimates</h4>
                    <table class="table">
                        <tr>
                            <td class="label">Total Flight Distance</td>
                            <td><strong>{{ number_format($location['distance_m'], 0) }} m</strong> ({{ number_format($location['distance_km'], 2) }} km)</td>
                            <td class="label">Pure Flight Duration</td>
                            <td><strong>{{ $location['pure_flight_time_s'] > 0 ? gmdate('H:i:s', $location['pure_flight_time_s']) : '00:00:00' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="label">Estimated Sorties (Batteries)</td>
                            <td>{{ $location['sortie_count'] }} sortie(s)</td>
                            <td class="label">Total Photo Exposures</td>
                            <td>{{ number_format($location['total_images']) }} images</td>
                        </tr>
                        <tr>
                            <td class="label">Survey Flight Lines</td>
                            <td>{{ $location['flight_lines'] }} main line(s)</td>
                            <td class="label">Coverage Area</td>
                            <td>{{ number_format($location['boundary_area'], 2) }} m&sup2; ({{ number_format($location['boundary_area_ha'], 2) }} ha)</td>
                        </tr>
                    </table>

                @else
                    <!-- SBES SPECIFICATIONS -->
                    <h4>Hydrographic Survey Parameters</h4>
                    <table class="table">
                        <tr>
                            <td class="label">Sounding Distance</td>
                            <td><strong>{{ number_format($location['distance_nm'], 4) }} NM</strong></td>
                            <td class="label">Survey Speed</td>
                            <td>{{ number_format($location['survey_speed_knots'], 1) }} knots</td>
                        </tr>
                        <tr>
                            <td class="label">Working Hours Per Day</td>
                            <td>{{ number_format($location['working_hours_per_day'], 1) }} hours/day</td>
                            <td class="label">Estimated Sounding Hours</td>
                            <td><strong>{{ number_format($location['survey_hours'], 2) }} hours</strong></td>
                        </tr>
                        <tr>
                            <td class="label">Daily Execution Period</td>
                            <td><strong>{{ number_format($location['execution_days'], 2) }} days</strong> ({{ number_format($location['execution_days_ceil']) }} rounded days)</td>
                            <td class="label">Survey Tracklines</td>
                            <td>{{ $location['line_count'] }} lines ({{ $location['main_line_count'] }} main, {{ $location['cross_line_count'] }} cross)</td>
                        </tr>
                        <tr>
                            <td class="label">Boundaries Count</td>
                            <td>{{ $location['boundary_count'] }} boundary polygon(s)</td>
                            <td class="label">Survey Area Coverage</td>
                            <td>{{ number_format($location['boundary_area'], 2) }} m&sup2; ({{ number_format($location['boundary_area_ha'], 2) }} ha)</td>
                        </tr>
                    </table>
                @endif

                <!-- MAP SCREENSHOT -->
                @if($location['screenshot'])
                    <div class="map-box">
                        <div style="font-size: 9px; font-weight: bold; color: #0f2d4a; margin-bottom: 5px;">
                            Survey Plan & Trackline Coverage Map &mdash; {{ $location['name'] }}
                        </div>
                        <img src="{{ $location['screenshot'] }}" alt="Survey Map for {{ $location['name'] }}">
                        <div class="map-caption">
                            Satellite / Topographic overlay showing planned survey boundaries and trajectory lines.
                        </div>
                    </div>
                @endif
            @endif
        </div>
    @empty
        <div class="alert-box">No survey areas have been registered for this project.</div>
    @endforelse

    <!-- REPORT FOOTER -->
    <div class="footer">
        Eco Hydrotech Solutions &bull; Survey Planning & Technical Estimation &bull; Generated {{ $generated_at->format('Y-m-d H:i:s') }}
    </div>

</body>
</html>
