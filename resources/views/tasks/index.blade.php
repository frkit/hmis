@extends('layouts.dashboard')

@section('title', 'Task Management')
@section('page-title', 'Task Management')

@section('extra-styles')
<style>
/* Stat row */
.task-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:22px; }
.t-stat { background:var(--surface); border:1px solid var(--border); border-radius:12px;
    padding:16px 18px; display:flex; align-items:center; gap:14px; }
.t-stat-icon { width:40px; height:40px; border-radius:10px; display:flex; align-items:center;
    justify-content:center; font-size:16px; flex-shrink:0; }
.t-stat-val { font-size:24px; font-weight:700; line-height:1; }
.t-stat-lbl { font-size:11px; color:var(--text-muted); font-weight:500; margin-top:2px; }

/* Filter bar */
.filter-bar { background:var(--surface); border:1px solid var(--border); border-radius:12px;
    padding:12px 16px; display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:18px; }
.filter-bar input, .filter-bar select {
    background:var(--surface2); border:1px solid var(--border); color:var(--text);
    font-family:'Inter',sans-serif; font-size:13px; padding:7px 11px; border-radius:8px; outline:none; }
.filter-bar input:focus, .filter-bar select:focus { border-color:var(--accent); }

/* Task table */
.task-wrap { background:var(--surface); border:1px solid var(--border); border-radius:14px; overflow:hidden; }
.task-table { width:100%; border-collapse:collapse; }
.task-table th { background:var(--surface2); padding:11px 15px; text-align:left; font-size:11px;
    font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px;
    border-bottom:1px solid var(--border); }
.task-table td { padding:12px 15px; border-bottom:1px solid var(--border); font-size:13px; vertical-align:middle; }
.task-table tr:last-child td { border-bottom:none; }
.task-table tr:hover td { background:var(--surface2); }

/* Priority badge */
.pri-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 8px;
    border-radius:6px; font-size:11px; font-weight:600; }

/* Status select */
.status-form select { background:transparent; border:none; font-size:12px; font-family:'Inter',sans-serif;
    font-weight:600; cursor:pointer; outline:none; padding:3px 6px; border-radius:6px; }

/* Action btn */
.act-btn { width:28px; height:28px; border-radius:6px; border:1px solid var(--border);
    display:inline-flex; align-items:center; justify-content:center;
    font-size:11px; cursor:pointer; text-decoration:none; color:var(--text-muted);
    background:var(--surface2); transition:all .15s; }
.act-btn:hover { color:var(--text); }
.act-btn.danger:hover { color:#f85149; border-color:#f85149; }

.btn-primary { display:inline-flex; align-items:center; gap:7px; padding:9px 18px; border-radius:9px;
    background:var(--accent); color:#fff; font-size:13px; font-weight:600; border:none; cursor:pointer;
    font-family:'Inter',sans-serif; text-decoration:none; transition:all .15s; }
.btn-primary:hover { background:var(--accent-dk); }

.alert-success { background:rgba(46,204,113,0.1); border:1px solid rgba(46,204,113,0.3); color:#2ecc71;
    padding:11px 15px; border-radius:9px; margin-bottom:16px; font-size:13px; }

.overdue-row td { background: rgba(248,81,73,0.04) !important; }

@media(max-width:900px){ .task-stats{grid-template-columns:repeat(2,1fr);} }
</style>
@endsection

@section('content')

@if(session('success'))
<div class="alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif

<!-- Header -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
    <div>
        <h1 style="font-size:21px;font-weight:700;">Task Management</h1>
        <div style="font-size:13px;color:var(--text-muted);margin-top:2px;">Assign and track tasks across all HMIS modules</div>
    </div>
    <a href="{{ route('tasks.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> New Task</a>
</div>

<!-- Stats -->
<div class="task-stats">
    <div class="t-stat">
        <div class="t-stat-icon" style="background:rgba(139,148,158,0.12);color:#8b949e;"><i class="fa-solid fa-list-check"></i></div>
        <div><div class="t-stat-val">{{ $counts['total'] }}</div><div class="t-stat-lbl">Total Tasks</div></div>
    </div>
    <div class="t-stat">
        <div class="t-stat-icon" style="background:rgba(139,148,158,0.12);color:#8b949e;"><i class="fa-solid fa-clock"></i></div>
        <div><div class="t-stat-val" style="color:#8b949e;">{{ $counts['pending'] }}</div><div class="t-stat-lbl">Pending</div></div>
    </div>
    <div class="t-stat">
        <div class="t-stat-icon" style="background:rgba(59,158,255,0.12);color:#3b9eff;"><i class="fa-solid fa-spinner"></i></div>
        <div><div class="t-stat-val" style="color:#3b9eff;">{{ $counts['in_progress'] }}</div><div class="t-stat-lbl">In Progress</div></div>
    </div>
    <div class="t-stat">
        <div class="t-stat-icon" style="background:rgba(46,204,113,0.12);color:#2ecc71;"><i class="fa-solid fa-circle-check"></i></div>
        <div><div class="t-stat-val" style="color:#2ecc71;">{{ $counts['completed'] }}</div><div class="t-stat-lbl">Completed</div></div>
    </div>
</div>

<!-- Filters -->
<form method="GET" class="filter-bar">
    <input type="text" name="search" placeholder="🔍 Search tasks..." value="{{ request('search') }}" style="min-width:180px;">
    <select name="status">
        <option value="">All Status</option>
        @foreach(\App\Models\Task::$statuses as $k => $s)
        <option value="{{ $k }}" {{ request('status')==$k?'selected':'' }}>{{ $s['label'] }}</option>
        @endforeach
    </select>
    <select name="priority">
        <option value="">All Priority</option>
        @foreach(\App\Models\Task::$priorities as $k => $p)
        <option value="{{ $k }}" {{ request('priority')==$k?'selected':'' }}>{{ $p['label'] }}</option>
        @endforeach
    </select>
    <select name="module">
        <option value="">All Modules</option>
        @foreach($modules as $k => $v)
        <option value="{{ $k }}" {{ request('module')==$k?'selected':'' }}>{{ $v }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-primary" style="padding:7px 14px;font-size:12px;">
        <i class="fa-solid fa-filter"></i> Filter
    </button>
    <a href="{{ route('tasks.index') }}" style="font-size:12px;color:var(--text-muted);text-decoration:none;padding:7px 10px;">
        <i class="fa-solid fa-xmark"></i> Clear
    </a>
</form>

<!-- Table -->
<div class="task-wrap">
    @if($tasks->count())
    <table class="task-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Task</th>
                <th>Module</th>
                <th>Priority</th>
                <th>Assigned To</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($tasks as $i => $task)
        <tr class="{{ $task->isOverdue() ? 'overdue-row' : '' }}">
            <td style="color:var(--text-muted);width:40px;">{{ $tasks->firstItem()+$i }}</td>
            <td>
                <div style="font-weight:600;max-width:240px;">{{ $task->title }}</div>
                @if($task->description)
                <div style="font-size:11px;color:var(--text-muted);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:240px;">
                    {{ $task->description }}
                </div>
                @endif
            </td>
            <td>
                <span style="display:inline-flex;align-items:center;gap:4px;font-size:11.5px;color:var(--text-muted);
                    background:var(--surface2);padding:3px 8px;border-radius:6px;">
                    {{ $modules[$task->module] ?? ucfirst($task->module) }}
                </span>
            </td>
            <td>
                @php $pr = \App\Models\Task::$priorities[$task->priority] ?? ['label'=>$task->priority,'color'=>'#999']; @endphp
                <span class="pri-badge"
                    style="background:{{ $pr['color'] }}18;color:{{ $pr['color'] }};">
                    {{ $pr['label'] }}
                </span>
            </td>
            <td>
                @if($task->assignee)
                <div style="display:flex;align-items:center;gap:7px;">
                    <div style="width:26px;height:26px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);
                        display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#fff;">
                        {{ strtoupper(substr($task->assignee->name,0,1)) }}
                    </div>
                    <span>{{ $task->assignee->name }}</span>
                </div>
                @else
                <span style="color:var(--text-muted);font-size:12px;">Unassigned</span>
                @endif
            </td>
            <td>
                @if($task->due_date)
                    <span style="{{ $task->isOverdue() ? 'color:#f85149;font-weight:600;' : 'color:var(--text-muted);' }}">
                        @if($task->isOverdue())<i class="fa-solid fa-triangle-exclamation"></i> @endif
                        {{ $task->due_date->format('M d, Y') }}
                    </span>
                @else
                    <span style="color:var(--text-muted);font-size:12px;">No date</span>
                @endif
            </td>
            <td>
                <form method="POST" action="{{ route('tasks.update-status', $task) }}">
                    @csrf
                    @php $st = \App\Models\Task::$statuses[$task->status] ?? ['color'=>'#999']; @endphp
                    <select name="status" onchange="this.form.submit()"
                        style="color:{{ $st['color'] }};background:{{ $st['color'] }}14;border:none;
                            padding:4px 8px;border-radius:6px;font-size:12px;font-weight:600;
                            font-family:'Inter',sans-serif;cursor:pointer;outline:none;">
                        @foreach(\App\Models\Task::$statuses as $k => $s)
                        <option value="{{ $k }}" {{ $task->status==$k?'selected':'' }}
                            style="color:var(--text);background:var(--surface2);">{{ $s['label'] }}</option>
                        @endforeach
                    </select>
                </form>
            </td>
            <td>
                <div style="display:flex;gap:5px;">
                    <a href="{{ route('tasks.edit',$task) }}" class="act-btn" title="Edit">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <form method="POST" action="{{ route('tasks.destroy',$task) }}"
                          onsubmit="return confirm('Delete this task?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="act-btn danger" title="Delete">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @if($tasks->hasPages())
    <div style="padding:12px 16px;border-top:1px solid var(--border);">
        {{ $tasks->links('pagination::simple-bootstrap-4') }}
    </div>
    @endif
    @else
    <div style="text-align:center;padding:60px 20px;color:var(--text-muted);">
        <i class="fa-solid fa-list-check" style="font-size:38px;margin-bottom:12px;display:block;"></i>
        <div style="font-size:15px;font-weight:600;margin-bottom:6px;">No tasks found</div>
        <a href="{{ route('tasks.create') }}" class="btn-primary" style="margin-top:12px;">
            <i class="fa-solid fa-plus"></i> Create First Task
        </a>
    </div>
    @endif
</div>

@endsection
