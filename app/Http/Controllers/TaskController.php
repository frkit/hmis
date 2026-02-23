<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Task::with(['assignee', 'assigner']);

        // Non-admins only see their own tasks
        if (!auth()->user()->isAdmin()) {
            $query->where(function($q) {
                $q->where('assigned_to', auth()->id())
                  ->orWhere('assigned_by', auth()->id());
            });
        }

        if ($request->filled('status'))   $query->where('status',   $request->status);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
        if ($request->filled('module'))   $query->where('module',   $request->module);
        if ($request->filled('search'))   $query->where('title', 'like', '%'.$request->search.'%');

        $tasks    = $query->orderByRaw("FIELD(status,'in_progress','pending','completed','cancelled')")
                          ->orderByRaw("FIELD(priority,'urgent','high','medium','low')")
                          ->paginate(20)->withQueryString();

        $users    = User::where('is_active', true)->orderBy('name')->get();
        $modules  = $this->moduleList();

        $counts = [
            'total'       => Task::count(),
            'pending'     => Task::where('status','pending')->count(),
            'in_progress' => Task::where('status','in_progress')->count(),
            'completed'   => Task::where('status','completed')->count(),
        ];

        return view('tasks.index', compact('tasks', 'users', 'modules', 'counts'));
    }

    public function create()
    {
        $users   = User::where('is_active', true)->orderBy('name')->get();
        $modules = $this->moduleList();
        return view('tasks.create', compact('users', 'modules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:191',
            'description' => 'nullable|string',
            'module'      => 'required|string',
            'priority'    => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date'    => 'nullable|date',
        ]);

        Task::create([
            'title'       => $request->title,
            'description' => $request->description,
            'module'      => $request->module,
            'priority'    => $request->priority,
            'status'      => 'pending',
            'due_date'    => $request->due_date,
            'assigned_to' => $request->assigned_to,
            'assigned_by' => auth()->id(),
        ]);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function edit(Task $task)
    {
        $users   = User::where('is_active', true)->orderBy('name')->get();
        $modules = $this->moduleList();
        return view('tasks.edit', compact('task', 'users', 'modules'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title'       => 'required|string|max:191',
            'description' => 'nullable|string',
            'module'      => 'required|string',
            'priority'    => 'required|in:low,medium,high,urgent',
            'status'      => 'required|in:pending,in_progress,completed,cancelled',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date'    => 'nullable|date',
        ]);

        $task->update($request->only([
            'title','description','module','priority','status','due_date','assigned_to',
        ]));

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return back()->with('success', 'Task deleted.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $task->update(['status' => $request->status]);
        return back()->with('success', 'Task status updated.');
    }

    private function moduleList(): array
    {
        return [
            'general'      => 'General',
            'registration' => 'Registration',
            'appointment'  => 'Appointment',
            'laboratory'   => 'Laboratory',
            'radiology'    => 'Radiology',
            'pharmacy'     => 'Pharmacy',
            'ipd'          => 'IPD',
            'opd'          => 'OPD',
            'billing'      => 'Billing',
            'hr'           => 'HR',
            'inventory'    => 'Inventory',
            'it'           => 'IT',
        ];
    }
}
