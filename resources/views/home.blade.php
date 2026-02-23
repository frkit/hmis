@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endsection

@section('extra-styles')
<style>
    /* Light mode */
    body.light-mode {
        --bg:        #f4f6f9;
        --surface:   #ffffff;
        --surface2:  #f0f2f5;
        --border:    #dde1e7;
        --text:      #1a1f2e;
        --text-muted:#6b7280;
    }
    body.light-mode .sidebar  { box-shadow: 2px 0 12px rgba(0,0,0,0.08); }
    body.light-mode .topbar   { box-shadow: 0 1px 8px rgba(0,0,0,0.06); }
    body.light-mode .stat-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.12); }

    /* ── Stats Cards ── */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 24px;
    }

    .stat-card {
        border: none;
        border-radius: 16px;
        padding: 24px 22px 20px;
        display: flex;
        flex-direction: column;
        gap: 14px;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        color: #fff;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 32px rgba(0,0,0,0.35);
    }
    /* Shiny overlay effect */
    .stat-card::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 130px; height: 130px;
        border-radius: 50%;
        background: rgba(255,255,255,0.12);
    }
    .stat-card::after {
        content: '';
        position: absolute;
        bottom: -50px; left: -20px;
        width: 160px; height: 160px;
        border-radius: 50%;
        background: rgba(255,255,255,0.07);
    }
    .stat-card.green  { background: linear-gradient(135deg, #2ecc71 0%, #1a9e52 100%); }
    .stat-card.blue   { background: linear-gradient(135deg, #3b9eff 0%, #1565c0 100%); }
    .stat-card.purple { background: linear-gradient(135deg, #a855f7 0%, #6d28d9 100%); }
    .stat-card.orange { background: linear-gradient(135deg, #f97316 0%, #c2410c 100%); }

    .stat-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        position: relative; z-index: 1;
    }
    .stat-icon {
        width: 46px; height: 46px;
        border-radius: 12px;
        background: rgba(255,255,255,0.2);
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; color: #fff;
        flex-shrink: 0;
        backdrop-filter: blur(4px);
    }

    .stat-trend {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 20px;
        background: rgba(255,255,255,0.2);
        color: #fff;
        backdrop-filter: blur(4px);
    }

    .stat-value {
        font-size: 36px;
        font-weight: 800;
        letter-spacing: -1.5px;
        color: #fff;
        line-height: 1;
        position: relative; z-index: 1;
    }
    .stat-label {
        font-size: 13px;
        color: rgba(255,255,255,0.85);
        font-weight: 500;
    }
    .stat-sub {
        font-size: 11.5px;
        color: rgba(255,255,255,0.65);
        margin-top: 2px;
    }

    /* ── Two-column row ── */
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        margin-bottom: 24px;
    }
    .grid-3-1 {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 18px;
        margin-bottom: 24px;
    }

    /* ── Panel ── */
    .panel {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
    }
    .panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 22px;
        border-bottom: 1px solid var(--border);
    }
    .panel-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
        display: flex; align-items: center; gap: 8px;
    }
    .panel-title i { color: var(--text-muted); font-size: 13px; }
    .panel-action {
        font-size: 12px;
        color: var(--info);
        text-decoration: none;
        font-weight: 500;
        transition: opacity 0.15s;
    }
    .panel-action:hover { opacity: 0.7; }
    .panel-body { padding: 18px 22px; }

    /* ── Activity Feed ── */
    .activity-list { display: flex; flex-direction: column; gap: 0; }
    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 14px 0;
        border-bottom: 1px solid var(--border);
    }
    .activity-item:last-child { border-bottom: none; }

    .act-dot {
        width: 36px; height: 36px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; flex-shrink: 0; margin-top: 1px;
    }
    .act-dot.green  { background:rgba(46,204,113,0.12); color:#2ecc71; }
    .act-dot.blue   { background:rgba(88,166,255,0.12); color:#58a6ff; }
    .act-dot.purple { background:rgba(188,140,255,0.12); color:#bc8cff; }
    .act-dot.orange { background:rgba(240,136,62,0.12); color:#f0883e; }
    .act-dot.red    { background:rgba(248,81,73,0.12);  color:#f85149; }

    .act-content { flex: 1; }
    .act-desc { font-size: 13px; color: var(--text); line-height: 1.5; }
    .act-desc strong { font-weight: 600; }
    .act-time { font-size: 11px; color: var(--text-muted); margin-top: 3px; }

    /* ── Quick Stats bar ── */
    .quick-row {
        display: grid;
        grid-template-columns: repeat(3,1fr);
        gap: 0;
    }
    .quick-item {
        text-align: center;
        padding: 18px 8px;
        border-right: 1px solid var(--border);
    }
    .quick-item:last-child { border-right: none; }
    .quick-val { font-size: 22px; font-weight: 700; color: var(--text); letter-spacing: -0.5px; }
    .quick-lbl { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px; margin-top: 3px; }

    /* ── Department table ── */
    .dept-table { width: 100%; border-collapse: collapse; }
    .dept-table th {
        text-align: left; font-size: 11px; font-weight: 600;
        color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;
        padding: 10px 0; border-bottom: 1px solid var(--border);
    }
    .dept-table td { padding: 13px 0; border-bottom: 1px solid var(--border); font-size: 13px; vertical-align: middle; }
    .dept-table tr:last-child td { border-bottom: none; }
    .dept-table td:last-child, .dept-table th:last-child { text-align: right; }

    .progress-wrap { background: var(--surface2); border-radius: 20px; height: 5px; overflow: hidden; width: 100px; }
    .progress-bar  { height: 100%; border-radius: 20px; }

    .status-pill {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 9px; border-radius: 20px;
        font-size: 11px; font-weight: 600;
    }
    .status-pill.normal   { background:rgba(46,204,113,0.12); color:#2ecc71; }
    .status-pill.busy     { background:rgba(240,136,62,0.12);  color:#f0883e; }
    .status-pill.critical { background:rgba(248,81,73,0.12);   color:#f85149; }

    /* ── Page header ── */
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
    }
    .page-header h1 { font-size: 22px; font-weight: 700; letter-spacing: -0.5px; }
    .page-header .date { font-size: 13px; color: var(--text-muted); margin-top: 3px; }

    .header-actions { display: flex; gap: 10px; }
    .btn-sm {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px; border-radius: 8px;
        font-size: 13px; font-weight: 500;
        cursor: pointer; border: none;
        font-family: 'Inter', sans-serif;
        transition: all 0.15s;
        text-decoration: none;
    }
    .btn-primary { background: var(--accent); color: #fff; }
    .btn-primary:hover { background: var(--accent-dk); box-shadow: 0 4px 14px rgba(46,204,113,0.35); }
    .btn-outline { background: transparent; color: var(--text-muted); border: 1px solid var(--border); }
    .btn-outline:hover { color:var(--text); background: var(--surface2); }

    /* ── Charts grid ── */
    .charts-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 18px;
        margin-bottom: 24px;
    }
    .chart-wrap {
        position: relative;
        padding: 8px 0 0;
        height: 280px;
    }
    .chart-wrap canvas { width:100% !important; height:100% !important; }
    @media (max-width: 1000px) { .charts-grid { grid-template-columns: 1fr; } }

    @media (max-width: 700px) {
        .stats-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
        .grid-2     { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1>Good {{ date('H') < 12 ? 'Morning' : (date('H') < 17 ? 'Afternoon' : 'Evening') }}, {{ explode(' ', auth()->user()->name)[0] }} 👋</h1>
        <div class="date">{{ now()->format('l, F j, Y') }}</div>
    </div>
    <div class="header-actions">
        <a href="#" class="btn-sm btn-outline"><i class="fa-solid fa-download"></i> Export</a>
        <a href="#" class="btn-sm btn-primary"><i class="fa-solid fa-plus"></i> New Patient</a>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card green">
        <div class="stat-top">
            <div class="stat-icon green"><i class="fa-solid fa-user-injured"></i></div>
            <div class="stat-trend up"><i class="fa-solid fa-arrow-trend-up"></i> 12%</div>
        </div>
        <div>
            <div class="stat-value">1,284</div>
            <div class="stat-label">Total Patients</div>
            <div class="stat-sub">+48 this week</div>
        </div>
    </div>
    <div class="stat-card blue">
        <div class="stat-top">
            <div class="stat-icon blue"><i class="fa-solid fa-calendar-check"></i></div>
            <div class="stat-trend up"><i class="fa-solid fa-arrow-trend-up"></i> 8%</div>
        </div>
        <div>
            <div class="stat-value">36</div>
            <div class="stat-label">Today's Appointments</div>
            <div class="stat-sub">12 completed · 24 pending</div>
        </div>
    </div>
    <div class="stat-card purple">
        <div class="stat-top">
            <div class="stat-icon purple"><i class="fa-solid fa-bed-pulse"></i></div>
            <div class="stat-trend down"><i class="fa-solid fa-arrow-trend-down"></i> 3%</div>
        </div>
        <div>
            <div class="stat-value">58</div>
            <div class="stat-label">In-Patients (IPD)</div>
            <div class="stat-sub">87% bed occupancy</div>
        </div>
    </div>
    <div class="stat-card orange">
        <div class="stat-top">
            <div class="stat-icon orange"><i class="fa-solid fa-file-invoice-dollar"></i></div>
            <div class="stat-trend up"><i class="fa-solid fa-arrow-trend-up"></i> 5%</div>
        </div>
        <div>
            <div class="stat-value">$24.8k</div>
            <div class="stat-label">Revenue (This Month)</div>
            <div class="stat-sub">$3.2k pending</div>
        </div>
    </div>
</div>

<!-- Row: Activity + Quick Stats -->
<div class="grid-3-1">

    <!-- Recent Activity -->
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title"><i class="fa-solid fa-clock-rotate-left"></i> Recent Activity</div>
            <a href="#" class="panel-action">View all</a>
        </div>
        <div class="panel-body" style="padding:0 22px;">
            <ul class="activity-list">
                <li class="activity-item">
                    <div class="act-dot green"><i class="fa-solid fa-user-plus"></i></div>
                    <div class="act-content">
                        <div class="act-desc"><strong>New patient registered</strong> — Ali Hassan (ID #12094)</div>
                        <div class="act-time"><i class="fa-regular fa-clock"></i> 5 minutes ago</div>
                    </div>
                </li>
                <li class="activity-item">
                    <div class="act-dot blue"><i class="fa-solid fa-calendar-plus"></i></div>
                    <div class="act-content">
                        <div class="act-desc"><strong>Appointment scheduled</strong> — Dr. Sarah with Patient #11983</div>
                        <div class="act-time"><i class="fa-regular fa-clock"></i> 22 minutes ago</div>
                    </div>
                </li>
                <li class="activity-item">
                    <div class="act-dot purple"><i class="fa-solid fa-flask"></i></div>
                    <div class="act-content">
                        <div class="act-desc"><strong>Lab results ready</strong> — CBC report for Fatima Khan</div>
                        <div class="act-time"><i class="fa-regular fa-clock"></i> 47 minutes ago</div>
                    </div>
                </li>
                <li class="activity-item">
                    <div class="act-dot orange"><i class="fa-solid fa-file-invoice"></i></div>
                    <div class="act-content">
                        <div class="act-desc"><strong>Invoice generated</strong> — $840 for Patient Ahmad Raza</div>
                        <div class="act-time"><i class="fa-regular fa-clock"></i> 1 hour ago</div>
                    </div>
                </li>
                <li class="activity-item">
                    <div class="act-dot red"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <div class="act-content">
                        <div class="act-desc"><strong>Critical alert</strong> — Low stock: Amoxicillin 500mg</div>
                        <div class="act-time"><i class="fa-regular fa-clock"></i> 2 hours ago</div>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <!-- Quick Stats -->
    <div style="display:flex; flex-direction:column; gap:18px;">
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title"><i class="fa-solid fa-chart-pie"></i> At a Glance</div>
            </div>
            <div class="quick-row">
                <div class="quick-item">
                    <div class="quick-val" style="color:#2ecc71;">12</div>
                    <div class="quick-lbl">Doctors</div>
                </div>
                <div class="quick-item">
                    <div class="quick-val" style="color:#58a6ff;">34</div>
                    <div class="quick-lbl">Nurses</div>
                </div>
                <div class="quick-item">
                    <div class="quick-val" style="color:#bc8cff;">67</div>
                    <div class="quick-lbl">Beds</div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div class="panel-title"><i class="fa-solid fa-circle-check"></i> Today's Summary</div>
            </div>
            <div class="panel-body" style="display:flex;flex-direction:column;gap:12px;">
                @php
                    $summary = [
                        ['label'=>'OPD Visits',      'val'=>42,  'color'=>'#2ecc71'],
                        ['label'=>'Surgeries',        'val'=>3,   'color'=>'#58a6ff'],
                        ['label'=>'Discharges',       'val'=>7,   'color'=>'#bc8cff'],
                        ['label'=>'Lab Tests Done',   'val'=>89,  'color'=>'#f0883e'],
                    ];
                @endphp
                @foreach($summary as $s)
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:13px;color:var(--text-muted);">{{ $s['label'] }}</span>
                    <span style="font-size:14px;font-weight:700;color:{{ $s['color'] }}">{{ $s['val'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

<!-- Charts Row -->
<div class="charts-grid" style="margin-bottom:24px;">

    <!-- Bar Chart: Monthly Patients -->
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title"><i class="fa-solid fa-chart-column"></i> Monthly Patient Visits</div>
            <span style="font-size:12px;color:var(--text-muted);">Last 12 months</span>
        </div>
        <div class="panel-body">
            <div class="chart-wrap"><canvas id="barChart"></canvas></div>
        </div>
    </div>

    <!-- Donut Chart: Department Distribution -->
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title"><i class="fa-solid fa-chart-pie"></i> Patients by Dept.</div>
            <span style="font-size:12px;color:var(--text-muted);">This month</span>
        </div>
        <div class="panel-body">
            <div class="chart-wrap" style="height:240px;">
                <canvas id="donutChart"></canvas>
            </div>
            <!-- Legend -->
            <div style="display:flex;flex-wrap:wrap;gap:8px 14px;margin-top:14px;">
                @php
                $legend = [
                    ['Emergency','#f85149'],['Cardiology','#3b9eff'],
                    ['Orthopedics','#2ecc71'],['Pediatrics','#a855f7'],
                    ['Pulmonology','#f97316'],['Radiology','#fbbf24'],
                ];
                @endphp
                @foreach($legend as $l)
                <div style="display:flex;align-items:center;gap:5px;font-size:11.5px;color:var(--text-muted);">
                    <span style="width:9px;height:9px;border-radius:50%;background:{{ $l[1] }};display:inline-block;"></span>
                    {{ $l[0] }}
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

<!-- Department Status -->
<div class="panel">
    <div class="panel-header">
        <div class="panel-title"><i class="fa-solid fa-building-columns"></i> Department Status</div>
        <a href="#" class="panel-action">Manage</a>
    </div>
    <div class="panel-body">
        <table class="dept-table">
            <thead>
                <tr>
                    <th>Department</th>
                    <th>Head Doctor</th>
                    <th>Patients</th>
                    <th>Occupancy</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $depts = [
                        ['Emergency',    'Dr. Ahmed',     18, 90, 'critical', '#f85149'],
                        ['Cardiology',   'Dr. Sara',      12, 75, 'busy',     '#f0883e'],
                        ['Orthopedics',  'Dr. Khalid',    8,  50, 'normal',   '#2ecc71'],
                        ['Pediatrics',   'Dr. Fatima',    10, 62, 'busy',     '#f0883e'],
                        ['Pulmonology',  'Dr. Hassan',    6,  40, 'normal',   '#2ecc71'],
                        ['Radiology',    'Dr. Nadia',     14, 70, 'busy',     '#f0883e'],
                    ];
                @endphp
                @foreach($depts as $d)
                <tr>
                    <td style="font-weight:600;">{{ $d[0] }}</td>
                    <td style="color:var(--text-muted);">{{ $d[1] }}</td>
                    <td style="font-weight:600;">{{ $d[2] }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div class="progress-wrap">
                                <div class="progress-bar" style="width:{{ $d[3] }}%;background:{{ $d[4] }};"></div>
                            </div>
                            <span style="font-size:12px;color:var(--text-muted);">{{ $d[3] }}%</span>
                        </div>
                    </td>
                    <td>
                        <span class="status-pill {{ $d[2] === 18 ? 'critical' : ($d[3] > 60 ? 'busy' : 'normal') }}">
                            <i class="fa-solid fa-circle" style="font-size:6px;"></i>
                            {{ ucfirst($d[2] === 18 ? 'critical' : ($d[3] > 60 ? 'busy' : 'normal')) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isLight = document.body.classList.contains('light-mode');
    const gridColor  = isLight ? 'rgba(0,0,0,0.06)'  : 'rgba(255,255,255,0.06)';
    const textColor  = isLight ? '#6b7280'            : '#8b949e';
    const tooltipBg  = isLight ? '#ffffff'            : '#1c2128';
    const tooltipTxt = isLight ? '#1a1f2e'            : '#e6edf3';

    Chart.defaults.color = textColor;
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size   = 12;

    /* ── BAR CHART ── */
    const barCtx = document.getElementById('barChart').getContext('2d');
    const months = ['Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','Jan','Feb'];

    const gradBar = barCtx.createLinearGradient(0, 0, 0, 260);
    gradBar.addColorStop(0, 'rgba(59,158,255,0.9)');
    gradBar.addColorStop(1, 'rgba(59,158,255,0.2)');

    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Patients',
                data: [820,940,880,1020,1150,1080,1220,1300,1180,1260,1350,1284],
                backgroundColor: gradBar,
                borderColor: '#3b9eff',
                borderWidth: 0,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: tooltipBg,
                    titleColor: tooltipTxt,
                    bodyColor: textColor,
                    borderColor: isLight ? '#dde1e7' : '#30363d',
                    borderWidth: 1,
                    padding: 10,
                    callbacks: {
                        label: ctx => ' ' + ctx.parsed.y.toLocaleString() + ' visits'
                    }
                }
            },
            scales: {
                x: { grid: { color: gridColor }, ticks: { color: textColor } },
                y: { grid: { color: gridColor }, ticks: { color: textColor },
                     beginAtZero: false, min: 700 }
            }
        }
    });

    /* ── DONUT CHART ── */
    const donutCtx = document.getElementById('donutChart').getContext('2d');
    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: ['Emergency','Cardiology','Orthopedics','Pediatrics','Pulmonology','Radiology'],
            datasets: [{
                data: [18, 12, 8, 10, 6, 14],
                backgroundColor: ['#f85149','#3b9eff','#2ecc71','#a855f7','#f97316','#fbbf24'],
                borderColor: isLight ? '#ffffff' : '#161b22',
                borderWidth: 3,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: tooltipBg,
                    titleColor: tooltipTxt,
                    bodyColor: textColor,
                    borderColor: isLight ? '#dde1e7' : '#30363d',
                    borderWidth: 1,
                    padding: 10,
                    callbacks: {
                        label: ctx => ' ' + ctx.parsed + ' patients  (' +
                            Math.round(ctx.parsed / 68 * 100) + '%)'  
                    }
                }
            }
        }
    });
});
</script>
@endsection
