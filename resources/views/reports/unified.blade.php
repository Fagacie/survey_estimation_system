<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Survey & Cost Estimation — {{ $project->name }}</title>
    <style>
        @page { margin: 15mm 15mm 25mm 15mm; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9.5pt;
            color: #1e293b;
            line-height: 1.5;
        }

        /* ── HEADER ──────────────────────────── */
        .header-table {
            width: 100%;
            margin-bottom: 20px;
            margin-top: 10px;
        }
        .header-table td {
            vertical-align: top;
        }
        .company-name {
            font-size: 12pt;
            font-weight: bold;
            color: #000;
            margin-bottom: 4px;
        }
        .company-address {
            font-size: 10pt;
            color: #000;
            line-height: 1.3;
        }

        /* ── TITLE BAR ───────────────────────── */
        .title-table {
            width: 100%;
            border-bottom: 2px solid #1a5c8b;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }
        .title-table td {
            vertical-align: bottom;
        }
        .doc-title {
            font-size: 20pt;
            font-weight: bold;
            color: #1a5c8b;
            letter-spacing: 1px;
        }
        .doc-meta {
            text-align: right;
            font-size: 10pt;
            color: #1a5c8b;
            font-weight: bold;
        }

        /* ── INFO BOX ────────────────────────── */
        .info-box {
            width: 100%;
            border: 1px solid #a0a0a0;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-box td {
            padding: 10px 15px;
            vertical-align: top;
            border: 1px solid #a0a0a0;
        }
        .info-label {
            font-size: 9pt;
            color: #1a5c8b;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 10pt;
            color: #000;
        }

        /* ── SECTION TITLE ───────────────────── */
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #1a5c8b;
            border-bottom: 2px solid #a0a0a0;
            margin-top: 25px;
            margin-bottom: 12px;
            padding-bottom: 4px;
        }

        /* ── DATA TABLE (key-value pairs) ────── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border: 1px solid #a0a0a0;
        }
        .data-table td {
            padding: 10px 12px;
            border: 1px solid #a0a0a0;
            font-size: 9.5pt;
        }
        .data-table .label {
            background: #eef4f9;
            font-weight: bold;
            color: #1a5c8b;
            width: 25%;
            font-size: 9pt;
        }

        /* ── COST TABLE (with header) ────────── */
        .cost-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border: 1px solid #a0a0a0;
        }
        .cost-table thead th {
            background: #1a5c8b;
            color: #fff;
            padding: 10px 12px;
            font-size: 9pt;
            font-weight: bold;
            text-align: left;
            border: 1px solid #a0a0a0;
        }
        .cost-table thead th.text-right {
            text-align: right;
        }
        .cost-table tbody td {
            padding: 10px 12px;
            border: 1px solid #a0a0a0;
            font-size: 9.5pt;
        }
        .cost-table .cat-row td {
            background: #eef4f9;
            font-weight: bold;
            color: #1a5c8b;
            font-size: 9.5pt;
        }
        .cost-table .sub-row td {
            background: #f8fafc;
            font-weight: bold;
            border-top: 1px solid #a0a0a0;
        }
        .cost-table .grand-row td {
            background: #fff;
            color: #1a5c8b;
            font-weight: bold;
            font-size: 11pt;
            border-top: 2px solid #a0a0a0;
        }
        .text-right {
            text-align: right;
        }

        /* ── AREA BOX ────────────────────────── */
        .area-box {
            border: 1px solid #a0a0a0;
            border-top: 4px solid #1a5c8b;
            margin-bottom: 20px;
            background: #fff;
        }
        .area-header {
            background: #f8fafc;
            padding: 10px 15px;
            border-bottom: 1px solid #a0a0a0;
        }
        .area-header table { width: 100%; }
        .area-name {
            font-weight: bold;
            font-size: 11pt;
            color: #0f172a;
        }

        /* ── SIGNATURES ──────────────────────── */
        .sig-table {
            width: 100%;
            margin-top: 50px;
            margin-bottom: 40px;
        }
        .sig-table td {
            width: 50%;
            vertical-align: top;
            padding: 0 15px;
        }
        .sig-label {
            color: #1a5c8b;
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 60px;
        }
        .sig-line {
            border-bottom: 2px dotted #000;
            margin-bottom: 5px;
            width: 85%;
        }
        .sig-name {
            font-weight: bold;
            font-size: 10pt;
        }
        .sig-role {
            font-size: 9pt;
            color: #64748b;
        }

        /* ── FOOTER ──────────────────────────── */
        .footer-table {
            width: 100%;
            margin-top: 50px;
            padding-bottom: 15px;
        }
        .footer-tagline {
            font-size: 11pt;
            font-weight: bold;
            color: #333;
            line-height: 1.4;
        }
        .footer-tagline .blue { color: #1a5c8b; }
        .footer-tagline .green { color: #4a8c2a; }
        .footer-contact {
            font-size: 10pt;
            color: #000;
            line-height: 1.5;
            text-align: right;
        }
        .footer-bottom-border {
            height: 12px;
            width: 100%;
            /* DomPDF linear-gradient workaround: use a background color, or fallback to solid */
            background: #1a5c8b;
        }
        
        .page-break { page-break-before: always; }
    </style>
</head>
<body>

    {{-- 1. HEADER (Matches Invoice: Logo Left, Company Right) --}}
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 40%; vertical-align: middle;">
                @if($company['company_logo'] ?? false)
                    <img src="{{ storage_path('app/public/' . $company['company_logo']) }}" alt="Logo" style="max-height: 80px; max-width: 220px;">
                @elseif(file_exists(public_path('images/logo.png')))
                    <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="max-height: 80px; max-width: 220px;">
                @endif
            </td>
            <td style="width: 60%; text-align: right;">
                <div class="company-name">
                    {{ $company['company_name'] ?? 'ECO HYDROTECH SOLUTIONS SDN. BHD.' }} 
                    @if($company['company_registration'] ?? false)
                        ({{ $company['company_registration'] }})
                    @endif
                </div>
                <div class="company-address">
                    {!! nl2br(e($company['company_address'] ?? "Institute of Oceanography and Environment\nUniversiti Malaysia Terengganu\n21030, Kuala Nerus, Terengganu\nMalaysia")) !!}
                </div>
            </td>
        </tr>
    </table>

    {{-- 2. TITLE BAR (Matches Invoice: Title Left, Meta Right) --}}
    <table class="title-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 50%;">
                <div class="doc-title">SURVEY &amp; COST ESTIMATION</div>
            </td>
            <td style="width: 50%; text-align: right;">
                <div style="color: #4a8c2a; font-weight: bold; font-size: 10pt; margin-bottom: 4px;">ORIGINAL</div>
                <div class="doc-meta">Ref No. {{ $quotation_number }}</div>
            </td>
        </tr>
    </table>

    {{-- CLIENT & PROJECT INFO --}}
    <table class="info-box" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 50%;">
                <div class="info-label">PREPARED FOR</div>
                <div class="info-value" style="margin-bottom: 10px;">{{ $project->client?->name ?? 'Client Name' }}</div>
                @if($project->client?->address)
                    <div class="info-value" style="font-size: 9.5pt; margin-bottom: 10px;">{!! nl2br(e($project->client->address)) !!}</div>
                @endif
                @if($project->client?->contact_person)
                    <div style="font-size: 9.5pt;">
                        <span style="color: #1a5c8b; font-weight: bold;">Attn:</span>
                        {{ $project->client->contact_person }}
                    </div>
                @endif
            </td>
            <td style="width: 50%;">
                <div class="info-label">PROJECT</div>
                <div class="info-value" style="margin-bottom: 10px;">{{ $project->name }}</div>
                <div class="info-label">LOCATION</div>
                <div class="info-value">{{ $project->location ?? 'N/A' }}</div>
            </td>
        </tr>
    </table>

    {{-- SCOPE & ENGINEERING SUMMARY --}}
    <div class="section-title">1. Technical Scope of Work</div>
    <table class="data-table">
        <tr>
            <td class="label">Survey Type</td>
            <td>Single Beam Echo Sounder (SBES)</td>
            <td class="label">Total Survey Area</td>
            <td>{{ number_format($total_area_m2, 2) }} m² ({{ number_format($total_area_km2, 4) }} km²)</td>
        </tr>
        <tr>
            <td class="label">Total Survey Lines</td>
            <td>{{ $total_lines }} lines ({{ $main_line_count }} main, {{ $cross_line_count }} cross)</td>
            <td class="label">Total Distance</td>
            <td>{{ number_format($total_length_nm, 4) }} Nautical Miles (NM)</td>
        </tr>
        <tr>
            <td class="label">Line Spacing</td>
            <td>{{ $line_spacing }} m</td>
            <td class="label">Orientation Angle</td>
            <td>{{ $orientation_angle }}°</td>
        </tr>
    </table>

    {{-- PROJECT AREAS OVERVIEW --}}
    @if(count($locations_data) > 0)
        <div class="section-title">2. Project Areas Overview</div>

        @foreach($locations_data as $loc)
            <div class="area-box">
                <div class="area-header">
                    <table>
                        <tr>
                            <td class="area-name" style="width: 100%;">{{ $loc['name'] }}</td>
                        </tr>
                    </table>
                </div>
                <div style="padding: 15px;">
                    <table class="data-table" style="margin-bottom: 0;">
                        <tr>
                            <td class="label">Total Area</td>
                            <td>{{ number_format($loc['area_m2'], 2) }} m² ({{ number_format($loc['area_km2'], 4) }} km²)</td>
                            <td class="label">Survey Speed</td>
                            <td>{{ $loc['survey_speed_knots'] }} knots</td>
                        </tr>
                        <tr>
                            <td class="label">Total Lines</td>
                            <td>{{ $loc['main_line_count'] + $loc['cross_line_count'] }} lines ({{ $loc['main_line_count'] }} main, {{ $loc['cross_line_count'] }} cross)</td>
                            <td class="label">Total Distance</td>
                            <td>{{ number_format($loc['total_length_nm'], 4) }} NM</td>
                        </tr>
                    </table>

                    @if(!empty($loc['map_screenshot']))
                        <div style="margin-top: 15px; text-align: center;">
                            <img src="{{ $loc['map_screenshot'] }}" alt="Map for {{ $loc['name'] }}" style="max-width: 100%; max-height: 380px; border: 1px solid #a0a0a0;">
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @endif


    {{-- TIME ESTIMATION --}}
    <div class="section-title">3. Execution Time Estimation</div>
    <table class="cost-table">
        <thead>
            <tr>
                <th>Survey Area</th>
                <th class="text-right">Distance (NM)</th>
                <th class="text-right">Speed (kn)</th>
                <th class="text-right">Hrs/Day</th>
                <th class="text-right">Execution (Days)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($locations_data as $loc)
            <tr>
                <td>{{ $loc['name'] }}</td>
                <td class="text-right">{{ number_format($loc['total_length_nm'], 2) }}</td>
                <td class="text-right">{{ $loc['survey_speed_knots'] }}</td>
                <td class="text-right">{{ $loc['working_hours_per_day'] }}</td>
                <td class="text-right" style="font-weight: bold;">{{ number_format($loc['execution_days'], 2) }}</td>
            </tr>
            @endforeach
            <tr class="sub-row">
                <td colspan="4" class="text-right" style="color: #1a5c8b;">Total Survey Execution Days</td>
                <td class="text-right" style="color: #1a5c8b;">{{ number_format($duration['execution_days'], 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div style="font-size: 10pt; font-weight: bold; margin-bottom: 8px; color: #1a5c8b; margin-top: 20px;">Global Allowances &amp; Total Duration</div>
    <table class="data-table">
        <tr>
            <td class="label">Survey Execution</td>
            <td>{{ number_format($duration['execution_days'], 2) }} days</td>
            <td class="label">Weather Standby</td>
            <td>{{ $duration['weather_days'] }} days</td>
        </tr>
        <tr>
            <td class="label">MOB/DEMOB</td>
            <td>{{ $duration['mod_demod_days'] }} days</td>
            <td class="label">Patch Test</td>
            <td>{{ $duration['patch_test_days'] }} days</td>
        </tr>
        <tr>
            <td colspan="3" class="label" style="text-align: right; font-size: 10pt;">
                <strong>Total Project Duration</strong>
            </td>
            <td style="font-size: 11pt; color: #1a5c8b; font-weight: bold;">
                {{ number_format($duration['total_days'], 2) }} days
            </td>
        </tr>
    </table>


    {{-- COST BREAKDOWN --}}
    <div class="page-break"></div>
    <div class="section-title">4. Itemized Cost Quotation</div>
    <table class="cost-table">
        <thead>
            <tr>
                <th style="width: 5%">No.</th>
                <th style="width: 40%">Description</th>
                <th style="width: 10%" class="text-right">Days/Qty</th>
                <th style="width: 10%" class="text-right">Units/Pax</th>
                <th style="width: 15%" class="text-right">Unit Rate (RM)</th>
                <th style="width: 20%" class="text-right">Total (RM)</th>
            </tr>
        </thead>
        <tbody>
            @php $itemNo = 1; $grandTotal = 0; @endphp
            @foreach($grouped_costs as $category => $items)
                <tr class="cat-row"><td colspan="6">{{ $category }}</td></tr>
                @php $catTotal = 0; @endphp
                @foreach($items as $item)
                    <tr>
                        <td>{{ $itemNo++ }}</td>
                        <td>{{ $item->description }}</td>
                        <td class="text-right">{{ number_format($item->days, 2) }}</td>
                        <td class="text-right">{{ $item->units ?? 1 }}</td>
                        <td class="text-right">{{ number_format($item->unit_rate, 2) }}</td>
                        <td class="text-right" style="font-weight: bold;">{{ number_format($item->total_price, 2) }}</td>
                    </tr>
                    @php $catTotal += $item->total_price; @endphp
                @endforeach
                <tr class="sub-row">
                    <td colspan="5" class="text-right" style="color: #475569;">{{ $category }} Subtotal</td>
                    <td class="text-right" style="color: #1a5c8b;">RM {{ number_format($catTotal, 2) }}</td>
                </tr>
                @php $grandTotal += $catTotal; @endphp
            @endforeach
            <tr class="grand-row">
                <td colspan="5" class="text-right">TOTAL DUE (MYR)</td>
                <td class="text-right">RM {{ number_format($grandTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- SIGNATURES --}}
    <table class="sig-table">
        <tr>
            <td>
                <div class="sig-label">Prepared By:</div>
                <div style="height: 50px;"></div>
                <div class="sig-line"></div>
                <div class="sig-name">{{ $project->user->name ?? 'System Administrator' }}</div>
                <div class="sig-role">Survey Operations</div>
            </td>
            <td>
                <div class="sig-label">Approved By:</div>
                <div style="height: 50px;"></div>
                <div class="sig-line"></div>
                <div class="sig-name">{{ $project->client?->name ?? 'Client Representative' }}</div>
                <div class="sig-role">Client</div>
            </td>
        </tr>
    </table>

    {{-- FOOTER (Matches Invoice) --}}
    <table class="footer-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 60%; vertical-align: middle;">
                <div class="footer-tagline">
                    "Integrated <span class="blue">Water</span> and <span class="green">Environmental</span> Solutions<br>
                    through <span class="green">Nature-Based Innovation</span>"
                </div>
            </td>
            <td style="width: 40%; vertical-align: middle;">
                <div class="footer-contact">
                    <div>{{ $company['company_email'] ?? 'info@ecohydrotechsolutions.com' }}</div>
                    <div>{{ $company['company_phone'] ?? '+60 19-464 0632' }}</div>
                </div>
            </td>
        </tr>
    </table>
    
    {{-- DomPDF trick: using a borderless table cell to simulate the linear gradient block at the bottom --}}
    <table style="width: 100%; border-collapse: collapse; margin-top: 15px;" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 50%; height: 15px; background-color: #1a5c8b;"></td>
            <td style="width: 50%; height: 15px; background-color: #4a8c2a;"></td>
        </tr>
    </table>

</body>
</html>