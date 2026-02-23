@extends('layouts.dashboard')

@section('title', 'Add New Doctor')
@section('page-title', 'Add New Doctor')

@section('extra-styles')
<style>
.form-card { background:var(--surface); border:1px solid var(--border); border-radius:14px; overflow:hidden; margin-bottom:18px; }
.form-section-title { font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase;
    letter-spacing:1px; padding:14px 20px 10px; border-bottom:1px solid var(--border);
    display:flex; align-items:center; gap:8px; }
.form-body { padding:20px; }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px; }
.form-group { display:flex; flex-direction:column; gap:5px; }
.form-group label { font-size:11px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.7px; }
.form-group input, .form-group select, .form-group textarea {
    background:var(--surface2); border:1px solid var(--border); color:var(--text);
    font-family:'Inter',sans-serif; font-size:13px; padding:9px 12px; border-radius:8px;
    outline:none; transition:border-color .2s; width:100%;
}
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color:var(--accent); }
.btn-primary { display:inline-flex; align-items:center; gap:7px; padding:10px 20px; border-radius:9px;
    background:var(--accent); color:#fff; font-size:13px; font-weight:600; border:none; cursor:pointer;
    font-family:'Inter',sans-serif; text-decoration:none; transition:all .15s; }
.btn-primary:hover { background:var(--accent-dk); }
.btn-outline { display:inline-flex; align-items:center; gap:7px; padding:10px 20px; border-radius:9px;
    background:transparent; color:var(--text-muted); font-size:13px; border:1px solid var(--border);
    cursor:pointer; font-family:'Inter',sans-serif; text-decoration:none; }
@media(max-width:700px){ .form-row{grid-template-columns:1fr;} }
</style>
@endsection

@section('content')

<div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
    <a href="{{ route('doctors.index') }}" style="color:var(--text-muted);text-decoration:none;font-size:13px;">
        <i class="fa-solid fa-arrow-left"></i> Back to Doctors
    </a>
    <i class="fa-solid fa-chevron-right" style="font-size:10px;color:var(--text-muted);"></i>
    <span style="font-size:13px;font-weight:600;">Add New Doctor</span>
</div>

<form action="{{ route('doctors.store') }}" method="POST">
    @csrf

    <div class="form-card">
        <div class="form-section-title"><i class="fa-solid fa-user-doctor"></i> Basic Information</div>
        <div class="form-body">
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" placeholder="Dr. John Doe" required value="{{ old('name') }}">
                    @error('name')<div style="color:var(--danger);font-size:11px;margin-top:2px;">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Department *</label>
                    <select name="department_id" required>
                        <option value="">Select Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Designation</label>
                    <input type="text" name="designation" placeholder="e.g. Senior Consultant" value="{{ old('designation') }}">
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" placeholder="+92 ..." value="{{ old('phone') }}">
                </div>
            </div>
        </div>
    </div>

    <div class="form-card">
        <div class="form-section-title"><i class="fa-solid fa-graduation-cap"></i> Clinical Details</div>
        <div class="form-body">
            <div class="form-row">
                <div class="form-group">
                    <label>Specialization *</label>
                    <input type="text" name="specialization" placeholder="e.g. Cardiology" required value="{{ old('specialization') }}">
                </div>
                <div class="form-group">
                    <label>Qualification</label>
                    <input type="text" name="qualification" placeholder="e.g. MBBS, FCPS" value="{{ old('qualification') }}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Consultation Fee (Rs.) *</label>
                    <input type="number" name="fee" required value="{{ old('fee', 0) }}">
                </div>
                <div class="form-group">
                    <label>Link System User (Optional)</label>
                    <select name="user_id">
                        <option value="">No System Access</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:12px;">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-save"></i> Register Doctor
        </button>
        <a href="{{ route('doctors.index') }}" class="btn-outline">Cancel</a>
    </div>
</form>
@endsection
