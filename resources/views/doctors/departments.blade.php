@extends('layouts.dashboard')

@section('title', 'Departments')
@section('page-title', 'Hospital Departments')

@section('extra-styles')
<style>
.dept-header {
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:22px; flex-wrap:wrap; gap:12px;
}
.dept-header h1 { font-size:22px; font-weight:700; letter-spacing:-.5px; }
.dept-header .sub { font-size:13px; color:var(--text-muted); margin-top:2px; }

/* Table */
.table-wrap {
    background:var(--surface); border:1px solid var(--border); border-radius:14px; overflow:hidden;
}
.std-table { width:100%; border-collapse:collapse; }
.std-table th {
    background:var(--surface2); padding:12px 16px; text-align:left;
    font-size:11px; font-weight:600; color:var(--text-muted);
    text-transform:uppercase; letter-spacing:1px; border-bottom:1px solid var(--border);
}
.std-table td { padding:13px 16px; border-bottom:1px solid var(--border); font-size:13px; vertical-align:middle; }
.std-table tr:last-child td { border-bottom:none; }
.std-table tr:hover td { background:var(--surface2); }

/* Badge */
.status-badge {
    display:inline-flex; align-items:center; gap:5px;
    padding:3px 9px; border-radius:20px; font-size:11px; font-weight:600;
}
.status-badge.active { background:rgba(46,204,113,0.12); color:#2ecc71; }
.status-badge.inactive { background:rgba(248,81,73,0.12); color:#f85149; }

.count-badge {
    background:var(--surface2); color:var(--accent); font-size:11px; font-weight:600;
    padding:2px 8px; border-radius:20px; border:1px solid var(--border);
}

/* Modal Styling */
.modal-content { background:var(--surface); border:1px solid var(--border); border-radius:16px; color:var(--text); }
.modal-header { border-bottom:1px solid var(--border); padding:18px 22px; }
.modal-title { font-size:16px; font-weight:700; }
.modal-body { padding:22px; }
.modal-footer { border-top:1px solid var(--border); padding:16px 22px; gap:10px; }

.form-group { display:flex; flex-direction:column; gap:6px; margin-bottom:16px; }
.form-group label { font-size:11px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.7px; }
.form-group input, .form-group textarea {
    background:var(--surface2); border:1px solid var(--border); color:var(--text);
    font-family:'Inter',sans-serif; font-size:13px; padding:10px 14px; border-radius:8px;
    outline:none; transition:border-color .2s; width:100%;
}
.form-group input:focus, .form-group textarea:focus { border-color:var(--accent); }

/* Action btns */
.act-btn {
    width:30px; height:30px; border-radius:7px; border:1px solid var(--border);
    display:inline-flex; align-items:center; justify-content:center;
    font-size:12px; cursor:pointer; text-decoration:none;
    color:var(--text-muted); background:var(--surface2); transition:all .15s;
}
.act-btn:hover { color:var(--text); background:var(--bg); }
.act-btn.danger:hover { color:#f85149; border-color:#f85149; }

.btn-primary {
    display:inline-flex; align-items:center; gap:7px;
    padding:9px 18px; border-radius:9px; background:var(--accent);
    color:#fff; font-size:13px; font-weight:600; border:none; cursor:pointer;
}
.btn-primary:hover { background:var(--accent-dk); }

.btn-outline {
    display:inline-flex; align-items:center; gap:7px;
    padding:8px 16px; border-radius:9px; border:1px solid var(--border);
    color:var(--text-muted); font-size:13px; font-weight:600; text-decoration:none; background:transparent;
}
.btn-outline:hover { background:var(--surface2); color:var(--text); }
</style>
@endsection

@section('content')

<div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
    <a href="{{ route('doctors.index') }}" style="color:var(--text-muted);text-decoration:none;font-size:13px;">
        <i class="fa-solid fa-arrow-left"></i> Back to Doctors
    </a>
    <i class="fa-solid fa-chevron-right" style="font-size:10px;color:var(--text-muted);"></i>
    <span style="font-size:13px;font-weight:600;">Department Management</span>
</div>

<div class="dept-header">
    <div>
        <h1>Hospital Departments</h1>
        <div class="sub">Manage organizational units and clinical services</div>
    </div>
    <button class="btn-primary" data-bs-toggle="modal" data-bs-target="#addDeptModal">
        <i class="fa-solid fa-plus"></i> New Department
    </button>
</div>

@if(session('success'))
    <div style="background:rgba(46,204,113,0.1); border:1px solid rgba(46,204,113,0.3); color:#2ecc71; padding:12px 16px; border-radius:10px; margin-bottom:18px; font-size:13px;">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background:rgba(248,81,73,0.1); border:1px solid rgba(248,81,73,.3); color:#f85149; padding:12px 16px; border-radius:10px; margin-bottom:18px; font-size:13px;">
        <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
    </div>
@endif

<div class="table-wrap">
    <table class="std-table">
        <thead>
            <tr>
                <th>Department Name</th>
                <th>Description</th>
                <th>Staff Count</th>
                <th>Status</th>
                <th style="text-align:right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($departments as $dept)
            <tr>
                <td style="font-weight:600;">{{ $dept->name }}</td>
                <td style="color:var(--text-muted);">{{ Str::limit($dept->description, 60) ?: '-' }}</td>
                <td><span class="count-badge">{{ $dept->doctors_count }} Doctors</span></td>
                <td>
                    <span class="status-badge {{ $dept->is_active ? 'active' : 'inactive' }}">
                        <i class="fa-solid fa-circle" style="font-size:6px;"></i>
                        {{ $dept->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>
                    <div style="display:flex; justify-content:flex-end; gap:8px;">
                        <button class="act-btn" data-bs-toggle="modal" data-bs-target="#editModal{{ $dept->id }}" title="Edit">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <form action="{{ route('departments.destroy', $dept) }}" method="POST" onsubmit="return confirm('Delete department?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="act-btn danger" title="Delete">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal{{ $dept->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Department</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('departments.update', $dept) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" name="name" value="{{ $dept->name }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea name="description" rows="3">{{ $dept->description }}</textarea>
                                        </div>
                                        <div style="display:flex;align-items:center;gap:8px;">
                                            <input type="hidden" name="is_active" value="0">
                                            <input type="checkbox" name="is_active" value="1" {{ $dept->is_active ? 'checked' : '' }} id="is_active{{ $dept->id }}">
                                            <label for="is_active{{ $dept->id }}" style="text-transform:none;letter-spacing:0;font-size:13px;color:var(--text);">Active Status</label>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn-outline" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted);">No departments found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addDeptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Department</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('departments.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Department Name</label>
                        <input type="text" name="name" placeholder="e.g. Cardiology" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="3" placeholder="Brief description..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-primary">Create Department</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
