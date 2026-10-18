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
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #1e293b;
            font-size: 9.5px;
            line-height: 1.45;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        /* ── Section Headings ─────────────────────── */
        h2 {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 10px 0 5px 0;
            padding: 4px 9px;
            background: #0f2d4a;
            color: #ffffff;
            page-break-after: avoid;
        }
        h4 {
            color: #0f2d4a;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 8px 0 3px 0;
            padding-bottom: 2px;
            border-bottom: 1px solid #94a3b8;
        }

        /* ── Document Masthead ────────────────────── */
        .hdr-accent-row {
            width: 100%;
            border-collapse: collapse;
            background: #1685b5;
        }
        .hdr-main {
            width: 100%;
            border-collapse: collapse;
            background: #102b46;
            margin-bottom: 8px;
        }
        .hdr-main td {
            vertical-align: top;
            padding: 12px 14px 11px;
            color: #ffffff;
        }
        .hdr-brand {
            color: #b9e6f7;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .hdr-title {
            color: #ffffff;
            font-size: 17px;
            font-weight: bold;
            margin: 0 0 4px;
            text-transform: uppercase;
        }
        .hdr-sub {
            color: #d9e5ef;
            font-size: 9px;
            font-weight: bold;
        }
        .hdr-meta {
            color: #c6d5e2;
            font-size: 8px;
            line-height: 1.65;
            margin-top: 8px;
        }
        .hdr-meta strong { color: #ffffff; }
        .hdr-right {
            width: 32%;
            text-align: right;
            border-left: 1px solid #38536c;
            vertical-align: middle;
        }

        /* ── Badges ───────────────────────────────── */
        .badge {
            display: inline;
            padding: 2px 6px;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-sbes    { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .badge-drone   { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-mixed   { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
        .badge-pending { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .badge-mapped  { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .badge-wh      { background: #1685b5; color: #ffffff; border: 1px solid #1685b5; }

        /* ── Data Tables ──────────────────────────── */
        .tbl {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
            margin-bottom: 6px;
            page-break-inside: avoid;
            border: 1px solid #94a3b8;
        }
        .tbl td {
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            font-size: 9px;
            color: #1e293b;
            background: #ffffff;
        }
        .tbl td.lbl {
            background: #f1f5f9;
            color: #0f2d4a;
            font-weight: bold;
            width: 26%;
        }

        /* ── KPI Tiles ────────────────────────────── */
        .kpi-tbl {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0 8px 0;
            page-break-inside: avoid;
            border: 1px solid #94a3b8;
        }
        .kpi-tbl td {
            width: 25%;
            padding: 8px 7px;
            border: 1px solid #cbd5e1;
            text-align: center;
            background: #ffffff;
            vertical-align: top;
        }
        .kpi-val  { color: #0f2d4a; font-size: 15px; font-weight: bold; line-height: 1.2; margin-bottom: 2px; }
        .kpi-unit { font-size: 9px; font-weight: normal; color: #0284c7; }
        .kpi-lbl  { color: #475569; font-size: 7.5px; font-weight: bold; text-transform: uppercase; }

        /* ── Area Block ───────────────────────────── */
        .area-wrap { margin-top: 6px; margin-bottom: 4px; }
        .area-hdr {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-left: 4px solid #0284c7;
            padding: 5px 9px;
            margin-bottom: 4px;
            page-break-after: avoid;
        }
        .area-hdr-drone {
            border-left-color: #059669;
        }
        .area-name { font-size: 11px; font-weight: bold; color: #0f2d4a; }

        /* ── Map Screenshot ───────────────────────── */
        .map-box {
            margin-top: 6px;
            page-break-inside: avoid;
            page-break-before: avoid;
            border: 1px solid #94a3b8;
            background: #f8fafc;
            padding: 5px;
        }
        .map-title {
            font-size: 8.5px;
            font-weight: bold;
            color: #0f2d4a;
            margin-bottom: 5px;
        }
        .map-box img {
            display: block;
            width: 100%;
            height: auto;
            border: 1px solid #94a3b8;
        }
        .map-cap {
            font-size: 7.5px;
            color: #64748b;
            margin-top: 4px;
            text-align: center;
            font-style: italic;
        }

        /* ── Alert ────────────────────────────────── */
        .alert {
            background: #fffbeb;
            border: 1px solid #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 9px 11px;
            margin: 6px 0;
            color: #92400e;
            font-size: 9px;
            page-break-inside: avoid;
        }

        /* ── Sign-off Block ──────────────────────── */
        .signoff-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .signoff-table td {
            width: 50%;
            padding: 6px 14px;
            vertical-align: top;
        }
        .signoff-line {
            border-bottom: 1px solid #94a3b8;
            height: 36px;
            margin-bottom: 4px;
        }

        /* ── Footer ───────────────────────────────── */
        .footer {
            margin-top: 12px;
            padding-top: 5px;
            border-top: 1px solid #e2e8f0;
            font-size: 8px;
            color: #64748b;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- ─── 1. DOCUMENT HEADER ─────────────────────── --}}
    <table class="hdr-accent-row"><tr><td style="height:4px; padding:0; font-size:1px; line-height:4px;">&nbsp;</td></tr></table>
    <table class="hdr-main">
        <tr>
            <td style="width:68%;">
                <div class="hdr-brand">Eco Hydrotech Solutions</div>
                <div class="hdr-title">{{ $report_title }}</div>
                <div class="hdr-sub">
                    {{ $is_single ? 'SURVEY AREA: ' . $report_subtitle : 'PROJECT: ' . $project->name }}
                </div>
                <div class="hdr-meta">
                    <strong>Reference:</strong> {{ $project->number ?? 'N/A' }}<br>
                    <strong>Generated:</strong> {{ $generated_at->format('d M Y, H:i') }}
                </div>
            </td>
            <td class="hdr-right">
                @if($report_type === 'drone')
                    <span class="badge badge-wh">UAV Photogrammetry</span>
                @elseif($report_type === 'sbes')
                    <span class="badge badge-wh">Hydrographic SBES</span>
                @else
                    <span class="badge badge-wh">Survey Operations</span>
                @endif
                <div class="hdr-meta" style="margin-top:8px;">
                    <strong>Project Period</strong><br>{{ $project->period ?? '&mdash;' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- ─── 2. PROJECT METADATA ─────────────────────── --}}
    <table class="tbl">
        <tr>
            <td class="lbl">Project Name</td>
            <td><strong>{{ $project->name }}</strong></td>
            <td class="lbl">Project Number</td>
            <td><strong>{{ $project->number ?? 'N/A' }}</strong></td>
        </tr>
        <tr>
            <td class="lbl">Client Organization</td>
            <td>{{ $project->client?->company_name ?? 'N/A' }}</td>
            <td class="lbl">Project Location</td>
            <td>{{ $project->location ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="lbl">Survey Scope</td>
            <td>{{ $is_single ? 'Individual Area Assessment' : 'Comprehensive Project Scope (' . $locations->count() . ' Areas)' }}</td>
            <td class="lbl">Report Classification</td>
            <td>Technical Survey Operations Estimation</td>
        </tr>
    </table>

    {{-- ─── 3. KPI SUMMARIES ─────────────────────────── --}}
    @if($report_type === 'sbes' || ($report_type === 'mixed' && $summary['sbes']['count'] > 0))
        <h2>Hydrographic Survey Summary (SBES)</h2>
        <table class="kpi-tbl">
            <tr>
                <td>
                    <div class="kpi-val">{{ number_format($summary['sbes']['total_distance_nm'], 4) }} <span class="kpi-unit">NM</span></div>
                    <div class="kpi-lbl">Total Survey Distance</div>
                </td>
                <td>
                    <div class="kpi-val">{{ number_format($summary['sbes']['total_hours'], 2) }} <span class="kpi-unit">hrs</span></div>
                    <div class="kpi-lbl">Estimated Survey Hours</div>
                </td>
                <td>
                    <div class="kpi-val">{{ number_format($summary['sbes']['execution_days'], 2) }} <span class="kpi-unit">days</span></div>
                    <div class="kpi-lbl">Execution Duration</div>
                </td>
                <td>
                    <div class="kpi-val">{{ number_format($summary['sbes']['total_days'], 2) }} <span class="kpi-unit">days</span></div>
                    <div class="kpi-lbl">Total Duration (w/ Allowances)</div>
                </td>
            </tr>
        </table>
        <table class="tbl">
            <tr>
                <td class="lbl">Weather Standby Allowance</td>
                <td>{{ number_format($summary['sbes']['weather_days'], 2) }} days</td>
                <td class="lbl">MOB / DEMOB Allowance</td>
                <td>{{ number_format($summary['sbes']['mod_demod_days'], 2) }} days</td>
            </tr>
            <tr>
                <td class="lbl">Patch Test Allowance</td>
                <td>{{ number_format($summary['sbes']['patch_test_days'], 2) }} days</td>
                <td class="lbl">Hydrographic Areas Included</td>
                <td>{{ $summary['sbes']['count'] }} Area(s)</td>
            </tr>
        </table>
    @endif

    @if($report_type === 'drone' || ($report_type === 'mixed' && $summary['drone']['count'] > 0))
        <h2>Drone Mapping Survey Summary (UAV Photogrammetry)</h2>
        <table class="kpi-tbl">
            <tr>
                <td>
                    <div class="kpi-val">{{ number_format($summary['drone']['total_distance_m'], 0) }} <span class="kpi-unit">m</span></div>
                    <div class="kpi-lbl">Flight Distance ({{ number_format($summary['drone']['total_distance_km'], 2) }} km)</div>
                </td>
                <td>
                    <div class="kpi-val">{{ number_format($summary['drone']['total_images']) }} <span class="kpi-unit">photos</span></div>
                    <div class="kpi-lbl">Total Exposures</div>
                </td>
                <td>
                    <div class="kpi-val">{{ number_format($summary['drone']['total_sorties']) }} <span class="kpi-unit">sorties</span></div>
                    <div class="kpi-lbl">Estimated Flights</div>
                </td>
                <td>
                    <div class="kpi-val">{{ number_format($summary['drone']['total_area_ha'], 2) }} <span class="kpi-unit">ha</span></div>
                    <div class="kpi-lbl">Survey Area ({{ number_format($summary['drone']['total_area_m2'], 2) }} m&sup2;)</div>
                </td>
            </tr>
        </table>
    @endif

    {{-- ─── 4. SURVEY AREA DETAIL ──────────────────── --}}
    <h2>Survey Areas &amp; Technical Specifications</h2>

    @forelse($locations as $index => $location)
        <div class="area-wrap">

            <div class="area-hdr {{ $location['is_drone'] ? 'area-hdr-drone' : '' }}">
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="vertical-align:middle; width:68%;">
                            <span class="area-name">{{ $index + 1 }}. {{ $location['name'] }}</span>
                            &nbsp;
                            @if($location['is_drone'])
                                <span class="badge badge-drone">Drone UAV</span>
                            @else
                                <span class="badge badge-sbes">Single Beam SBES</span>
                            @endif
                        </td>
                        <td style="text-align:right; vertical-align:middle;">
                            @if($location['is_mapped'])
                                <span class="badge badge-mapped">Mapped &amp; Planned</span>
                            @else
                                <span class="badge badge-pending">Planning Pending</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            @if(!$location['is_mapped'])
                <div class="alert">
                    <strong>Planning Notice:</strong> Survey line planning and boundary mapping for
                    <em>"{{ $location['name'] }}"</em> are currently pending.
                    Technical parameters and flight tracklines will become available once mapped in the survey planning module.
                </div>
            @else
                @if($location['is_drone'])
                    <h4>Equipment Platform &amp; Sensor</h4>
                    <table class="tbl">
                        <tr>
                            <td class="lbl">UAV Drone Model</td>
                            <td>{{ $location['drone_model'] }}</td>
                            <td class="lbl">Selected Camera / Sensor</td>
                            <td>{{ $location['camera_model'] }}</td>
                        </tr>
                    </table>

                    <h4>Flight &amp; Photogrammetric Parameters</h4>
                    <table class="tbl">
                        <tr>
                            <td class="lbl">Flight Altitude</td>
                            <td>{{ number_format($location['altitude_m'], 2) }} m AGL</td>
                            <td class="lbl">Ground Sampling Distance (GSD)</td>
                            <td>{{ number_format($location['gsd_cm'], 2) }} cm/px</td>
                        </tr>
                        <tr>
                            <td class="lbl">Forward Overlap</td>
                            <td>{{ number_format($location['front_overlap'], 1) }}%</td>
                            <td class="lbl">Side Overlap</td>
                            <td>{{ number_format($location['side_overlap'], 1) }}%</td>
                        </tr>
                        <tr>
                            <td class="lbl">Flight Speed</td>
                            <td>{{ number_format($location['speed_ms'], 1) }} m/s ({{ number_format($location['speed_ms'] * 3.6, 1) }} km/h)</td>
                            <td class="lbl">Course Heading / Angle</td>
                            <td>{{ number_format($location['course_angle'], 1) }}&deg;</td>
                        </tr>
                        <tr>
                            <td class="lbl">Photo Trigger Interval</td>
                            <td>{{ $location['photo_interval_s'] > 0 ? number_format($location['photo_interval_s'], 2) . ' s' : 'Distance based' }}</td>
                            <td class="lbl">Photo Baseline Spacing</td>
                            <td>{{ number_format($location['photo_spacing_m'], 1) }} m</td>
                        </tr>
                    </table>

                    <h4>Mission Execution Estimates</h4>
                    <table class="tbl">
                        <tr>
                            <td class="lbl">Total Flight Distance</td>
                            <td><strong>{{ number_format($location['distance_m'], 0) }} m</strong> ({{ number_format($location['distance_km'], 2) }} km)</td>
                            <td class="lbl">Pure Flight Duration</td>
                            <td><strong>{{ $location['pure_flight_time_s'] > 0 ? gmdate('H:i:s', $location['pure_flight_time_s']) : '00:00:00' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="lbl">Estimated Sorties (Batteries)</td>
                            <td>{{ $location['sortie_count'] }} sortie(s)</td>
                            <td class="lbl">Total Photo Exposures</td>
                            <td>{{ number_format($location['total_images']) }} images</td>
                        </tr>
                        <tr>
                            <td class="lbl">Survey Flight Lines</td>
                            <td>{{ $location['flight_lines'] }} main line(s)</td>
                            <td class="lbl">Coverage Area</td>
                            <td>{{ number_format($location['boundary_area'], 2) }} m&sup2; ({{ number_format($location['boundary_area_ha'], 2) }} ha)</td>
                        </tr>
                    </table>

                @else
                    <h4>Hydrographic Survey Parameters</h4>
                    <table class="tbl">
                        <tr>
                            <td class="lbl">Sounding Distance</td>
                            <td><strong>{{ number_format($location['distance_nm'], 4) }} NM</strong></td>
                            <td class="lbl">Survey Speed</td>
                            <td>{{ number_format($location['survey_speed_knots'], 1) }} knots</td>
                        </tr>
                        <tr>
                            <td class="lbl">Working Hours Per Day</td>
                            <td>{{ number_format($location['working_hours_per_day'], 1) }} hours/day</td>
                            <td class="lbl">Estimated Sounding Hours</td>
                            <td><strong>{{ number_format($location['survey_hours'], 2) }} hours</strong></td>
                        </tr>
                        <tr>
                            <td class="lbl">Daily Execution Period</td>
                            <td><strong>{{ number_format($location['execution_days'], 2) }} days</strong> ({{ number_format($location['execution_days_ceil']) }} rounded)</td>
                            <td class="lbl">Survey Tracklines</td>
                            <td>{{ $location['line_count'] }} lines ({{ $location['main_line_count'] }} main, {{ $location['cross_line_count'] }} cross)</td>
                        </tr>
                        <tr>
                            <td class="lbl">Boundaries Count</td>
                            <td>{{ $location['boundary_count'] }} boundary polygon(s)</td>
                            <td class="lbl">Survey Area Coverage</td>
                            <td>{{ number_format($location['boundary_area'], 2) }} m&sup2; ({{ number_format($location['boundary_area_ha'], 2) }} ha)</td>
                        </tr>
                    </table>
                @endif

                {{-- MAP SCREENSHOT --}}
                @if($location['screenshot'])
                    <div class="map-box">
                        <div class="map-title">
                            Survey Plan &amp; Trackline Coverage Map &mdash; {{ $location['name'] }}
                        </div>
                        <img src="{{ $location['screenshot'] }}" alt="Survey Map for {{ $location['name'] }}">
                        <div class="map-cap">
                            Satellite / topographic overlay showing planned survey boundaries and trajectory lines.
                        </div>
                    </div>
                @endif
            @endif

        </div>
    @empty
        <div class="alert">No survey areas have been registered for this project.</div>
    @endforelse

    {{-- ─── 5. SIGN-OFF BLOCK ─────────────────────── --}}
    <table class="signoff-table">
        <tr>
            <td>
                <div class="signoff-line"></div>
                <div style="font-weight: bold; font-size: 8.5px; color: #102b46;">Prepared By / Field Surveyor</div>
                <div style="font-size: 7.5px; color: #64748b;">Survey Planning &amp; Operations</div>
            </td>
            <td>
                <div class="signoff-line"></div>
                <div style="font-weight: bold; font-size: 8.5px; color: #102b46;">Approved By / Lead Hydrographer</div>
                <div style="font-size: 7.5px; color: #64748b;">Operations &amp; Quality Assurance</div>
            </td>
        </tr>
    </table>

    {{-- ─── FOOTER ─────────────────────────────────── --}}
    <div class="footer">
        Eco Hydrotech Solutions &bull; Survey Planning &amp; Technical Estimation &bull;
        Generated {{ $generated_at->format('Y-m-d H:i:s') }}
    </div>

</body>
</html>
