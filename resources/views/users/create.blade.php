@extends('layouts.dashboard')

@section('title', 'Add User')
@section('page-title', 'Add User')

@section('extra-styles')
<style>
@php
$moduleColors = [
    'registration'=>'#3b9eff','appointment'=>'#2ecc71','laboratory'=>'#a855f7',
    'radiology'=>'#f97316','pharmacy'=>'#fbbf24','ipd'=>'#f85149',
    'opd'=>'#06b6d4','billing'=>'#10b981','hr'=>'#8b5cf6','reports'=>'#d97706',
];
@endphp
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
.form-group input,.form-group select,.form-group textarea {
    background:var(--surface2); border:1px solid var(--border); color:var(--text);
    font-family:'Inter',sans-serif; font-size:13px;
    padding:10px 13px; border-radius:9px; outline:none; transition:border-color .2s;
    width:100%;
}
.form-group input:focus,.form-group select:focus { border-color:var(--accent); }
.form-group .hint { font-size:11px; color:var(--text-muted); }

/* Module permission grid */
.module-grid {
    display:grid; grid-template-columns:repeat(5,1fr); gap:10px; padding:18px 22px;
}
.module-card {
    display:flex; flex-direction:column; align-items:center; gap:7px;
    padding:14px 8px; border-radius:10px;
    border:1.5px solid var(--border); cursor:pointer;
    background:var(--surface2); transition:all .15s; text-align:center;
    user-select:none;
}
.module-card input[type=checkbox] { display:none; }
.module-card .mod-icon {
    width:38px; height:38px; border-radius:10px;
    display:flex; align-items:center; justify-content:center;
    font-size:16px; transition:all .15s;
}
.module-card .mod-label { font-size:11.5px; font-weight:600; color:var(--text-muted); transition:color .15s; }
.module-card.checked { border-color: var(--mc); background: color-mix(in srgb, var(--mc) 8%, var(--surface2)); }
.module-card.checked .mod-label { color: var(--mc); }

/* Buttons */
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

.error-msg { font-size:12px; color:#f85149; margin-top:4px; }

@media(max-width:700px){ .form-row{grid-template-columns:1fr;} .module-grid{grid-template-columns:repeat(2,1fr);} }
</style>
@endsection

@section('content')

<div style="display:flex;align-items:center;gap:10px;margin-bottom:22px;">
    <a href="{{ route('users.index') }}" style="color:var(--text-muted);text-decoration:none;font-size:13px;">
        <i class="fa-solid fa-arrow-left"></i> Back to Users
    </a>
    <i class="fa-solid fa-chevron-right" style="font-size:10px;color:var(--text-muted);"></i>
    <span style="font-size:13px;font-weight:600;">Add New User</span>
</div>

<form method="POST" action="{{ route('users.store') }}">
@csrf

<div style="display:flex; flex-direction:column; gap:18px;">

    <!-- Basic Info -->
    <div class="form-card">
        <div class="form-section-title"><i class="fa-solid fa-user"></i> Basic Information</div>
        <div class="form-body">
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Dr. Ali Hassan" required>
                    @error('name')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="user@hmis.com" required>
                    @error('email')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Designation *</label>
                    <select name="designation" id="designation" required onchange="checkDesignation()">
                        <option value="">Select Designation</option>
                        @foreach($designations as $key => $label)
                            <option value="{{ $key }}" {{ old('designation') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('designation')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+92 300 0000000">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" placeholder="Minimum 6 characters" required>
                    @error('password')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Confirm Password *</label>
                    <input type="password" name="password_confirmation" placeholder="Repeat password" required>
                </div>
            </div>
            <div class="form-group" style="flex-direction:row;align-items:center;gap:10px;margin-top:4px;">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked
                       style="width:16px;height:16px;accent-color:var(--accent);">
                <label for="is_active" style="text-transform:none;letter-spacing:0;font-size:13px;color:var(--text);cursor:pointer;">
                    Account is Active
                </label>
            </div>
        </div>
    </div>
    <!-- Clinical Details (Conditional) -->
    <div id="doctor_fields" class="form-card" style="display:none;">
        <div class="form-section-title"><i class="fa-solid fa-stethoscope"></i> Clinical Details (For Doctors)</div>
        <div class="form-body">
            <div class="form-row">
                <div class="form-group">
                    <label>Department *</label>
                    <select name="department_id" id="department_id">
                        <option value="">Select Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Specialization *</label>
                    <input type="text" name="specialization" value="{{ old('specialization') }}" placeholder="e.g. Cardiologist">
                    @error('specialization')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Consultation Fee (PKR) *</label>
                    <input type="number" name="fee" value="{{ old('fee', 0) }}" min="0">
                    @error('fee')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Qualification</label>
                    <input type="text" name="qualification" value="{{ old('qualification') }}" placeholder="e.g. MBBS, FCPS">
                </div>
            </div>
        </div>
    </div>

    <!-- Module Permissions -->
    <div class="form-card">
        <div class="form-section-title"><i class="fa-solid fa-key"></i> Module Access Permissions
            <span style="margin-left:auto;font-size:11px;color:var(--text-muted);text-transform:none;letter-spacing:0;font-weight:400;">
                Check the modules this user can access
            </span>
        </div>
        <div class="module-grid">
            @foreach($modules as $key => $mod)
            <label class="module-card" style="--mc:{{ $mod['color'] }};" id="card_{{ $key }}">
                <input type="checkbox" name="permissions[]" value="{{ $key }}"
                       {{ in_array($key, old('permissions', [])) ? 'checked' : '' }}
                       onchange="toggleCard('{{ $key }}', this)">
                <div class="mod-icon" id="icon_{{ $key }}"
                     style="background:{{ $mod['color'] }}22;color:{{ $mod['color'] }};">
                    <i class="fa-solid {{ $mod['icon'] }}"></i>
                </div>
                <div class="mod-label">{{ $mod['label'] }}</div>
            </label>
            @endforeach
        </div>
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
    </div>

    <!-- Actions -->
    <div style="display:flex;gap:12px;">
        <button type="submit" class="btn-primary"><i class="fa-solid fa-user-plus"></i> Create User</button>
        <a href="{{ route('users.index') }}" class="btn-outline">Cancel</a>
    </div>

</div>
</form>

@endsection

@section('scripts')
<script>
function toggleCard(key, cb) {
    document.getElementById('card_' + key).classList.toggle('checked', cb.checked);
}
function selectAll(val) {
    document.querySelectorAll('.module-card input[type=checkbox]').forEach(cb => {
        cb.checked = val;
        toggleCard(cb.value, cb);
    });
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

// Init on load
document.querySelectorAll('.module-card input[type=checkbox]').forEach(cb => {
    if (cb.checked) toggleCard(cb.value, cb);
});
checkDesignation();
</script>
@endsection
