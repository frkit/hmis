@extends('layouts.dashboard')

@section('title', 'Doctor Management')
@section('page-title', 'Doctor Management')

@section('extra-styles')
<style>
.doc-header {
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:22px; flex-wrap:wrap; gap:12px;
}
.doc-header h1 { font-size:22px; font-weight:700; letter-spacing:-.5px; }
.doc-header .sub { font-size:13px; color:var(--text-muted); margin-top:2px; }

/* Filters bar */
.filter-bar {
    background:var(--surface); border:1px solid var(--border); border-radius:12px;
    padding:14px 18px; display:flex; gap:12px; flex-wrap:wrap;
    align-items:center; margin-bottom:24px;
}
.filter-bar input, .filter-bar select {
    background:var(--surface2); border:1px solid var(--border); color:var(--text);
    font-family:'Inter',sans-serif; font-size:13px;
    padding:8px 12px; border-radius:8px; outline:none;
    transition: border-color .2s;
}
.filter-bar input:focus, .filter-bar select:focus { border-color:var(--accent); }
.filter-bar input { min-width:200px; }

/* Grid */
.doc-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 18px;
    margin-top: 10px;
}

.doc-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
    display: flex;
    flex-direction: column;
}

.doc-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    border-color: var(--accent);
}

.doc-card-header {
    background: var(--surface2);
    padding: 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    border-bottom: 1px solid var(--border);
}

.doc-avatar {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--accent), var(--accent-dk));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 700;
    flex-shrink: 0;
}

.doc-info h3 {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: var(--text);
}

.doc-info .specialization {
    font-size: 12px;
    color: var(--accent);
    font-weight: 600;
    display: block;
    margin-top: 1px;
}

.doc-card-body {
    padding: 18px;
    flex-grow: 1;
}

.doc-detail {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
    color: var(--text-muted);
    font-size: 12.5px;
}

.doc-detail i {
    width: 14px;
    color: var(--accent);
    text-align: center;
}

.doc-fee {
    font-size: 14px;
    font-weight: 700;
    color: var(--text);
    margin-top: 14px;
    padding-top: 14px;
    border-top: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.doc-actions {
    padding: 12px 18px;
    background: var(--surface2);
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}

.act-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    border: 1px solid var(--border);
    background: var(--surface);
    transition: all 0.2s;
    text-decoration: none;
    font-size: 12px;
}

.act-btn:hover {
    background: var(--accent);
    color: white;
    border-color: var(--accent);
}

.act-btn.danger:hover {
    background: var(--danger);
    border-color: var(--danger);
}

.btn-primary {
    display:inline-flex; align-items:center; gap:7px;
    padding:9px 18px; border-radius:9px; background:var(--accent);
    color:#fff; font-size:13px; font-weight:600; border:none; cursor:pointer;
    font-family:'Inter',sans-serif; text-decoration:none; transition:all .15s;
}
.btn-primary:hover { background:var(--accent-dk); box-shadow:0 4px 14px rgba(46,204,113,.35); }

.btn-outline {
    display:inline-flex; align-items:center; gap:7px;
    padding:8px 16px; border-radius:9px; border:1px solid var(--border);
    color:var(--text-muted); font-size:13px; font-weight:600; text-decoration:none; transition:all .15s;
}
.btn-outline:hover { background:var(--surface2); color:var(--text); }
</style>
@endsection

@section('content')

<div class="doc-header">
    <div>
        <h1>Doctors & Staff</h1>
        <div class="sub">Manage medical professionals and their clinical details</div>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('doctors.departments') }}" class="btn-outline">
            <i class="fa-solid fa-hospital"></i> Departments
        </a>
        <a href="{{ route('doctors.create') }}" class="btn-primary">
            <i class="fa-solid fa-user-doctor"></i> Add Doctor
        </a>
    </div>
</div>

@if(session('success'))
    <div style="background:rgba(46,204,113,0.1); border:1px solid rgba(46,204,113,0.3); color:#2ecc71; padding:12px 16px; border-radius:10px; margin-bottom:18px; font-size:13px;">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif

<form method="GET" action="{{ route('doctors.index') }}" class="filter-bar">
    <input type="text" name="search" placeholder="🔍 Search name or specialization..." value="{{ request('search') }}" style="flex:1;">
    <select name="department_id">
        <option value="">All Departments</option>
        @foreach($departments as $dept)
            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-primary" style="padding:8px 14px;">
        <i class="fa-solid fa-filter"></i> Filter
    </button>
</form>

<div class="doc-grid">
    @forelse($doctors as $doc)
        <div class="doc-card">
            <div class="doc-card-header">
                <div class="doc-avatar">
                    {{ strtoupper(substr($doc->name, 0, 1)) }}
                </div>
                <div class="doc-info">
                    <h3>Dr. {{ $doc->name }}</h3>
                    <span class="specialization">{{ $doc->specialization }}</span>
                </div>
            </div>
            <div class="doc-card-body">
                <div class="doc-detail">
                    <i class="fa-solid fa-graduation-cap"></i>
                    <span>{{ $doc->qualification }}</span>
                </div>
                <div class="doc-detail">
                    <i class="fa-solid fa-hospital-user"></i>
                    <span>{{ $doc->department->name ?? 'No Department' }}</span>
                </div>
                <div class="doc-detail">
                    <i class="fa-solid fa-phone"></i>
                    <span>{{ $doc->phone ?: 'No Phone' }}</span>
                </div>
                
                <div class="doc-fee">
                    <span>Consultation Fee</span>
                    <span>Rs. {{ number_format($doc->fee) }}</span>
                </div>
            </div>
            <div class="doc-actions">
                <a href="{{ route('doctors.edit', $doc) }}" class="act-btn" title="Edit Profile">
                    <i class="fa-solid fa-pen"></i>
                </a>
                <form action="{{ route('doctors.destroy', $doc) }}" method="POST" onsubmit="return confirm('Remove this doctor?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="act-btn danger" title="Remove">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="text-center py-5 w-100">
            <div class="mb-3">
                <i class="fa-solid fa-user-doctor-slash fa-4x text-muted"></i>
            </div>
            <h3>No Doctors Found</h3>
            <p class="text-muted">Start by adding your medical staff to the system.</p>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $doctors->links() }}
</div>
@endsection
