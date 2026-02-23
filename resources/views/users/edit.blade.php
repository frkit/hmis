@extends('layouts.dashboard')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('extra-styles')
<style>
.form-card {
    background:var(--surface); border:1px solid var(--border); border-radius:14px; overflow:hidden;
}
.form-section-title {
    font-size:12px; font-weight:600; color:var(--text-muted);
    text-transform:uppercase; letter-spacing:1px;
    padding:16px 22px 10px; border-bottom:1px solid var(--border);
    display:flex; align-items:center; gap:8px;
}
.form-body { padding:22px; }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; }
.form-group { display:flex; flex-direction:column; gap:6px; }
.form-group label { font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.7px; }
.form-group input,.form-group select {
    background:var(--surface2); border:1px solid var(--border); color:var(--text);
    font-family:'Inter',sans-serif; font-size:13px;
    padding:10px 13px; border-radius:9px; outline:none; transition:border-color .2s; width:100%;
}
.form-group input:focus,.form-group select:focus { border-color:var(--accent); }
.error-msg { font-size:12px; color:#f85149; margin-top:4px; }

/* Module grid */
.module-grid {
    display:grid; grid-template-columns:repeat(5,1fr); gap:10px; padding:18px 22px;
}
.module-card {
    display:flex; flex-direction:column; align-items:center; gap:7px;
    padding:14px 8px; border-radius:10px;
    border:1.5px solid var(--border); cursor:pointer;
    background:var(--surface2); transition:all .15s; text-align:center; user-select:none;
}
.module-card input[type=checkbox] { display:none; }
.module-card .mod-icon {
    width:38px; height:38px; border-radius:10px;
    display:flex; align-items:center; justify-content:center; font-size:16px;
}
.module-card .mod-label { font-size:11.5px; font-weight:600; color:var(--text-muted); transition:color .15s; }
.module-card.checked { border-color: var(--mc); background: color-mix(in srgb, var(--mc) 8%, var(--surface2)); }
.module-card.checked .mod-label { color: var(--mc); }

/* User info header */
.user-info-header {
    display:flex; align-items:center; gap:14px; padding:18px 22px;
    border-bottom:1px solid var(--border);
}
.u-avatar-lg {
    width:48px; height:48px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:18px; font-weight:700; color:#fff;
    background:linear-gradient(135deg,#667eea,#764ba2); flex-shrink:0;
}

.btn-primary {
    display:inline-flex; align-items:center; gap:7px;
    padding:10px 20px; border-radius:9px; background:var(--accent);
    color:#fff; font-size:13px; font-weight:600; border:none; cursor:pointer;
    font-family:'Inter',sans-serif; text-decoration:none; transition:all .15s;
}
.btn-primary:hover { background:var(--accent-dk); }
.btn-outline {
    display:inline-flex; align-items:center; gap:7px;
    padding:10px 20px; border-radius:9px; background:transparent;
    color:var(--text-muted); font-size:13px; font-weight:500; border:1px solid var(--border);
    cursor:pointer; font-family:'Inter',sans-serif; text-decoration:none; transition:all .15s;
}
.btn-outline:hover { color:var(--text); background:var(--surface2); }

@media(max-width:700px){ .form-row{grid-template-columns:1fr;} .module-grid{grid-template-columns:repeat(2,1fr);} }
</style>
@endsection

@section('content')

<div style="display:flex;align-items:center;gap:10px;margin-bottom:22px;">
    <a href="{{ route('users.index') }}" style="color:var(--text-muted);text-decoration:none;font-size:13px;">
        <i class="fa-solid fa-arrow-left"></i> Back to Users
    </a>
    <i class="fa-solid fa-chevron-right" style="font-size:10px;color:var(--text-muted);"></i>
    <span style="font-size:13px;font-weight:600;">Edit User</span>
</div>

<form method="POST" action="{{ route('users.update', $user) }}">
@csrf @method('PUT')

<div style="display:flex; flex-direction:column; gap:18px;">

    <!-- User header card -->
    <div class="form-card">
        <div class="user-info-header">
            <div class="u-avatar-lg">{{ strtoupper(substr($user->name,0,1)) }}</div>
            <div>
                <div style="font-size:16px;font-weight:700;">{{ $user->name }}</div>
                <div style="font-size:12px;color:var(--text-muted);">{{ $user->email }}
                    @if($user->isAdmin()) <span style="color:var(--accent);font-weight:600;"> · Admin</span>@endif
                </div>
                <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">
                    Joined {{ $user->created_at->format('M d, Y') }}
                </div>
            </div>
            <div style="margin-left:auto;">
                <span style="padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;
                    background:{{ $user->is_active ? 'rgba(46,204,113,0.12)' : 'rgba(248,81,73,0.12)' }};
                    color:{{ $user->is_active ? '#2ecc71' : '#f85149' }};">
                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Basic Info -->
    <div class="form-card">
        <div class="form-section-title"><i class="fa-solid fa-user"></i> Basic Information</div>
        <div class="form-body">
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Designation *</label>
                    <select name="designation" id="designation" required onchange="checkDesignation()">
                        <option value="">Select Designation</option>
                        @foreach($designations as $key => $label)
                            <option value="{{ $key }}" {{ old('designation', $user->designation) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+92 300 0000000">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>New Password <span style="font-weight:400;text-transform:none;">(leave blank to keep)</span></label>
                    <input type="password" name="password" placeholder="Minimum 6 characters">
                    @error('password')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="password_confirmation" placeholder="Repeat new password">
                </div>
            </div>
            <div class="form-group" style="flex-direction:row;align-items:center;gap:10px;margin-top:4px;">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                       style="width:16px;height:16px;accent-color:var(--accent);">
                <label for="is_active" style="text-transform:none;letter-spacing:0;font-size:13px;color:var(--text);cursor:pointer;">
                    Account is Active
                </label>
            </div>
        </div>
    </div>

    <!-- Clinical Details (Conditional) -->
    <div id="doctor_fields" class="form-card" style="{{ $user->isDoctor() ? 'display:block;' : 'display:none;' }}">
        <div class="form-section-title"><i class="fa-solid fa-stethoscope"></i> Clinical Details (For Doctors)</div>
        <div class="form-body">
            <div class="form-row">
                <div class="form-group">
                    <label>Department *</label>
                    <select name="department_id" id="department_id">
                        <option value="">Select Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Specialization *</label>
                    <input type="text" name="specialization" value="{{ old('specialization', $user->specialization) }}" placeholder="e.g. Cardiologist">
                    @error('specialization')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Consultation Fee (PKR) *</label>
                    <input type="number" name="fee" value="{{ old('fee', $user->fee) }}" min="0">
                    @error('fee')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Qualification</label>
                    <input type="text" name="qualification" value="{{ old('qualification', $user->qualification) }}" placeholder="e.g. MBBS, FCPS">
                </div>
            </div>
        </div>
    </div>

    <!-- Module Permissions -->
    <div class="form-card">
        <div class="form-section-title"><i class="fa-solid fa-key"></i> Module Access Permissions
            @if($user->isAdmin())
                <span style="margin-left:auto;font-size:11px;color:var(--accent);text-transform:none;letter-spacing:0;font-weight:600;">
                    <i class="fa-solid fa-infinity"></i> Admin has access to all modules
                </span>
            @else
                <span style="margin-left:auto;font-size:11px;color:var(--text-muted);text-transform:none;letter-spacing:0;font-weight:400;">
                    Check the modules this user can access
                </span>
            @endif
        </div>
        <div class="module-grid">
            @foreach($modules as $key => $mod)
            <label class="module-card {{ in_array($key, old('permissions', $user->permissions ?? [])) || $user->isAdmin() ? 'checked' : '' }}"
                   style="--mc:{{ $mod['color'] }};" id="card_{{ $key }}">
                <input type="checkbox" name="permissions[]" value="{{ $key }}"
                       {{ in_array($key, old('permissions', $user->permissions ?? [])) || $user->isAdmin() ? 'checked' : '' }}
                       {{ $user->isAdmin() ? 'disabled' : '' }}
                       onchange="toggleCard('{{ $key }}', this)">
                <div class="mod-icon" style="background:{{ $mod['color'] }}22;color:{{ $mod['color'] }};">
                    <i class="fa-solid {{ $mod['icon'] }}"></i>
                </div>
                <div class="mod-label">{{ $mod['label'] }}</div>
            </label>
            @endforeach
        </div>
        @if(!$user->isAdmin())
        <div style="padding:6px 20px 16px;display:flex;gap:10px;">
            <button type="button" onclick="selectAll(true)"
                    style="font-size:12px;color:var(--accent);background:none;border:none;cursor:pointer;font-family:'Inter',sans-serif;">
                <i class="fa-solid fa-check-double"></i> Select All
            </button>
            <button type="button" onclick="selectAll(false)"
                    style="font-size:12px;color:var(--text-muted);background:none;border:none;cursor:pointer;font-family:'Inter',sans-serif;">
                <i class="fa-solid fa-xmark"></i> Clear All
            </button>
        </div>
        @endif
    </div>

    <!-- Actions -->
    <div style="display:flex;gap:12px;">
        <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
        <a href="{{ route('users.index') }}" class="btn-outline">Cancel</a>
        @if($user->id !== auth()->id())
        <form method="POST" action="{{ route('users.destroy', $user) }}"
              onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.')"
              style="margin-left:auto;">
            @csrf @method('DELETE')
            <button type="submit" style="display:inline-flex;align-items:center;gap:7px;padding:10px 18px;border-radius:9px;
                background:rgba(248,81,73,0.1);color:#f85149;border:1px solid rgba(248,81,73,0.3);
                font-size:13px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;transition:all .15s;"
                    onmouseover="this.style.background='rgba(248,81,73,0.2)'"
                    onmouseout="this.style.background='rgba(248,81,73,0.1)'">
                <i class="fa-solid fa-trash"></i> Delete User
            </button>
        </form>
        @endif
    </div>

</div>
</form>

@endsection

@section('scripts')
<script>
function toggleCard(key, cb) {
    document.getElementById('card_' + key).classList.toggle('checked', cb.checked);
}
function checkDesignation() {
    const desig = document.getElementById('designation').value;
    const doctorFields = document.getElementById('doctor_fields');
    doctorFields.style.display = (desig === 'doctor') ? 'block' : 'none';

    // Toggle required attributes
    const dept = document.getElementById('department_id');
    if (desig === 'doctor') {
        dept.setAttribute('required', 'required');
    } else {
        dept.removeAttribute('required');
    }
}
checkDesignation();
</script>
@endsection
