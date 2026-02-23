@extends('layouts.dashboard')

@section('title', isset($task) ? 'Edit Task' : 'New Task')
@section('page-title', isset($task) ? 'Edit Task' : 'New Task')

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
    outline:none; transition:border-color .2s; width:100%; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color:var(--accent); }
.btn-primary { display:inline-flex; align-items:center; gap:7px; padding:10px 20px; border-radius:9px;
    background:var(--accent); color:#fff; font-size:13px; font-weight:600; border:none; cursor:pointer;
    font-family:'Inter',sans-serif; text-decoration:none; transition:all .15s; }
.btn-primary:hover { background:var(--accent-dk); }
.btn-outline { display:inline-flex; align-items:center; gap:7px; padding:10px 20px; border-radius:9px;
    background:transparent; color:var(--text-muted); font-size:13px; font-weight:500;
    border:1px solid var(--border); cursor:pointer; font-family:'Inter',sans-serif; text-decoration:none; }
@media(max-width:600px){ .form-row{grid-template-columns:1fr;} }
</style>
@endsection

@section('content')

<div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
    <a href="{{ route('tasks.index') }}" style="color:var(--text-muted);text-decoration:none;font-size:13px;">
        <i class="fa-solid fa-arrow-left"></i> Back to Tasks
    </a>
    <i class="fa-solid fa-chevron-right" style="font-size:10px;color:var(--text-muted);"></i>
    <span style="font-size:13px;font-weight:600;">{{ isset($task) ? 'Edit Task' : 'New Task' }}</span>
</div>

@php $isEdit = isset($task); @endphp

<form method="POST" action="{{ $isEdit ? route('tasks.update',$task) : route('tasks.store') }}">
@csrf
@if($isEdit) @method('PUT') @endif

<div class="form-card">
    <div class="form-section-title"><i class="fa-solid fa-list-check"></i> Task Details</div>
    <div class="form-body">
        <div class="form-group" style="margin-bottom:14px;">
            <label>Task Title *</label>
            <input type="text" name="title" value="{{ old('title', $task->title ?? '') }}"
                   placeholder="Enter a clear, actionable task title" required>
        </div>
        <div class="form-group" style="margin-bottom:14px;">
            <label>Description</label>
            <textarea name="description" rows="3" placeholder="Optional details or instructions..."
                      style="resize:vertical;">{{ old('description', $task->description ?? '') }}</textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Module *</label>
                <select name="module" required>
                    @foreach($modules as $k => $v)
                    <option value="{{ $k }}" {{ old('module', $task->module ?? '') == $k ? 'selected' : '' }}>
                        {{ $v }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Priority *</label>
                <select name="priority" required>
                    @foreach(\App\Models\Task::$priorities as $k => $p)
                    <option value="{{ $k }}" {{ old('priority', $task->priority ?? 'medium') == $k ? 'selected' : '' }}>
                        {{ $p['label'] }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Assign To</label>
                <select name="assigned_to">
                    <option value="">— Unassigned —</option>
                    @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ old('assigned_to', $task->assigned_to ?? '') == $u->id ? 'selected' : '' }}>
                        {{ $u->name }} ({{ $u->designation_label }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Due Date</label>
                <input type="date" name="due_date"
                       value="{{ old('due_date', isset($task->due_date) ? $task->due_date->format('Y-m-d') : '') }}">
            </div>
        </div>
        @if($isEdit)
        <div class="form-group">
            <label>Status</label>
            <select name="status" required>
                @foreach(\App\Models\Task::$statuses as $k => $s)
                <option value="{{ $k }}" {{ old('status',$task->status) == $k ? 'selected' : '' }}>
                    {{ $s['label'] }}
                </option>
                @endforeach
            </select>
        </div>
        @endif
    </div>
</div>

<div style="display:flex;gap:12px;">
    <button type="submit" class="btn-primary">
        <i class="fa-solid {{ $isEdit ? 'fa-floppy-disk' : 'fa-plus' }}"></i>
        {{ $isEdit ? 'Save Changes' : 'Create Task' }}
    </button>
    <a href="{{ route('tasks.index') }}" class="btn-outline">Cancel</a>
</div>

</form>
@endsection
