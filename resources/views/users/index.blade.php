@extends('layouts.dashboard')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('extra-styles')
<style>
.um-header {
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:22px; flex-wrap:wrap; gap:12px;
}
.um-header h1 { font-size:22px; font-weight:700; letter-spacing:-.5px; }
.um-header .sub { font-size:13px; color:var(--text-muted); margin-top:2px; }

/* Filters bar */
.filter-bar {
    background:var(--surface); border:1px solid var(--border); border-radius:12px;
    padding:14px 18px; display:flex; gap:12px; flex-wrap:wrap;
    align-items:center; margin-bottom:20px;
}
.filter-bar input, .filter-bar select {
    background:var(--surface2); border:1px solid var(--border); color:var(--text);
    font-family:'Inter',sans-serif; font-size:13px;
    padding:8px 12px; border-radius:8px; outline:none;
    transition: border-color .2s;
}
.filter-bar input:focus, .filter-bar select:focus { border-color:var(--accent); }
.filter-bar input { min-width:200px; }

/* Table */
.user-table-wrap {
    background:var(--surface); border:1px solid var(--border); border-radius:14px; overflow:hidden;
}
.user-table { width:100%; border-collapse:collapse; }
.user-table th {
    background:var(--surface2); padding:12px 16px; text-align:left;
    font-size:11px; font-weight:600; color:var(--text-muted);
    text-transform:uppercase; letter-spacing:1px; border-bottom:1px solid var(--border);
}
.user-table td { padding:13px 16px; border-bottom:1px solid var(--border); font-size:13px; vertical-align:middle; }
.user-table tr:last-child td { border-bottom:none; }
.user-table tr:hover td { background:var(--surface2); }

/* Avatar */
.u-avatar {
    width:36px; height:36px; border-radius:50%;
    display:inline-flex; align-items:center; justify-content:center;
    font-size:13px; font-weight:700; color:#fff; flex-shrink:0;
    background:linear-gradient(135deg,#667eea,#764ba2);
}

/* Designation badge */
.desig-badge {
    display:inline-flex; align-items:center; gap:5px;
    padding:3px 9px; border-radius:20px; font-size:11px; font-weight:600;
    background:var(--surface2); color:var(--text-muted);
}

/* Module dots */
.mod-dots { display:flex; gap:4px; flex-wrap:wrap; }
.mod-dot {
    width:22px; height:22px; border-radius:6px;
    display:inline-flex; align-items:center; justify-content:center;
    font-size:10px; cursor:default; position:relative;
}
.mod-dot[title]:hover::after {
    content: attr(title);
    position:absolute; bottom:110%; left:50%; transform:translateX(-50%);
    background:#000; color:#fff; padding:3px 7px; border-radius:5px;
    white-space:nowrap; font-size:10px; z-index:10;
    pointer-events:none;
}

/* Status pill */
.s-pill {
    display:inline-flex; align-items:center; gap:5px;
    padding:3px 8px; border-radius:20px; font-size:11px; font-weight:600;
}
.s-pill.active   { background:rgba(46,204,113,0.12); color:#2ecc71; }
.s-pill.inactive { background:rgba(248,81,73,0.12);  color:#f85149; }

/* Action btns */
.act-btn {
    width:30px; height:30px; border-radius:7px; border:1px solid var(--border);
    display:inline-flex; align-items:center; justify-content:center;
    font-size:12px; cursor:pointer; text-decoration:none;
    color:var(--text-muted); background:var(--surface2); transition:all .15s;
}
.act-btn:hover { color:var(--text); background:var(--bg); }
.act-btn.danger:hover { color:#f85149; border-color:#f85149; }
.act-btn.success:hover { color:#2ecc71; border-color:#2ecc71; }

/* Alert */
.alert-success { background:rgba(46,204,113,0.1); border:1px solid rgba(46,204,113,0.3); color:#2ecc71;
    padding:12px 16px; border-radius:10px; margin-bottom:18px; font-size:13px; }
.alert-error { background:rgba(248,81,73,0.1); border:1px solid rgba(248,81,73,.3); color:#f85149;
    padding:12px 16px; border-radius:10px; margin-bottom:18px; font-size:13px; }

.btn-primary {
    display:inline-flex; align-items:center; gap:7px;
    padding:9px 18px; border-radius:9px; background:var(--accent);
    color:#fff; font-size:13px; font-weight:600; border:none; cursor:pointer;
    font-family:'Inter',sans-serif; text-decoration:none; transition:all .15s;
}
.btn-primary:hover { background:var(--accent-dk); box-shadow:0 4px 14px rgba(46,204,113,.35); }

.empty-state { text-align:center; padding:60px 20px; color:var(--text-muted); }
.empty-state i { font-size:40px; margin-bottom:12px; display:block; }
</style>
@endsection

@section('content')

<div class="um-header">
    <div>
        <h1>User Management</h1>
        <div class="sub">Manage staff accounts, designations and module access</div>
    </div>
    <a href="{{ route('users.create') }}" class="btn-primary">
        <i class="fa-solid fa-user-plus"></i> Add User
    </a>
</div>

@if(session('success'))
    <div class="alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert-error"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}</div>
@endif

<!-- Filters -->
<form method="GET" action="{{ route('users.index') }}" class="filter-bar">
    <input type="text" name="search" placeholder="🔍  Search name or email..." value="{{ request('search') }}">
    <select name="designation">
        <option value="">All Designations</option>
        @foreach($designations as $key => $label)
            <option value="{{ $key }}" {{ request('designation') == $key ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    <select name="status">
        <option value="">All Status</option>
        <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>Active</option>
        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
    <button type="submit" class="btn-primary" style="padding:8px 14px;">
        <i class="fa-solid fa-filter"></i> Filter
    </button>
    <a href="{{ route('users.index') }}" class="act-btn" title="Clear filters" style="width:auto;padding:0 10px;height:36px;">
        <i class="fa-solid fa-xmark"></i> Clear
    </a>
</form>

<!-- Table -->
<div class="user-table-wrap">
    @if($users->count())
    <table class="user-table">
        <thead>
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Department</th>
                <th>Designation</th>
                <th>Module Access</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $i => $user)
            <tr>
                <td style="color:var(--text-muted);width:40px;">{{ $users->firstItem() + $i }}</td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="u-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                        <div>
                            <div style="font-weight:600;">{{ $user->name }}
                                @if($user->isAdmin()) <span style="font-size:10px;color:var(--accent);margin-left:4px;">[Admin]</span> @endif
                            </div>
                            <div style="font-size:11px;color:var(--text-muted);">{{ $user->email }}</div>
                            @if($user->phone)<div style="font-size:11px;color:var(--text-muted);"><i class="fa-solid fa-phone" style="font-size:9px;"></i> {{ $user->phone }}</div>@endif
                        </div>
                    </div>
                </td>
                <td>
                    <div style="font-weight:600; color:var(--text-muted); font-size:12px;">
                        {{ $user->department->name ?? '—' }}
                    </div>
                </td>
                <td>
                    <span class="desig-badge">
                        <i class="fa-solid fa-id-badge" style="font-size:10px;"></i>
                        {{ $user->designation_label }}
                    </span>
                </td>
                <td>
                    @if($user->isAdmin())
                        <span style="font-size:12px;color:var(--accent);font-weight:600;">
                            <i class="fa-solid fa-infinity"></i> All Modules
                        </span>
                    @elseif(empty($user->permissions))
                        <span style="font-size:12px;color:var(--text-muted);">No access</span>
                    @else
                        <div class="mod-dots">
                            @foreach($modules as $key => $mod)
                                @if(in_array($key, $user->permissions ?? []))
                                    <div class="mod-dot" title="{{ $mod['label'] }}"
                                         style="background:{{ $mod['color'] }}22;color:{{ $mod['color'] }};">
                                        <i class="fa-solid {{ $mod['icon'] }}"></i>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </td>
                <td>
                    <span class="s-pill {{ $user->is_active ? 'active' : 'inactive' }}">
                        <i class="fa-solid fa-circle" style="font-size:6px;"></i>
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>
                    <div style="display:flex;gap:6px;align-items:center;">
                        <a href="{{ route('users.edit', $user) }}" class="act-btn" title="Edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <form method="POST" action="{{ route('users.toggle-status', $user) }}">
                            @csrf
                            <button type="submit" class="act-btn {{ $user->is_active ? 'danger' : 'success' }}"
                                    title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                <i class="fa-solid {{ $user->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                            </button>
                        </form>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('users.destroy', $user) }}"
                              onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="act-btn danger" title="Delete">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <!-- Pagination -->
    @if($users->hasPages())
    <div style="padding:14px 18px;border-top:1px solid var(--border);">
        {{ $users->links('pagination::simple-bootstrap-4') }}
    </div>
    @endif
    @else
    <div class="empty-state">
        <i class="fa-solid fa-users-slash"></i>
        <div style="font-size:15px;font-weight:600;margin-bottom:6px;">No users found</div>
        <div style="font-size:13px;">Try adjusting your filters or add a new user.</div>
    </div>
    @endif
</div>

@endsection
