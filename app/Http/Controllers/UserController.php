<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // All HMIS modules
    public static array $modules = [
        'registration'  => ['icon' => 'fa-user-plus',         'label' => 'Registration',   'color' => '#3b9eff'],
        'appointment'   => ['icon' => 'fa-calendar-check',    'label' => 'Appointment',     'color' => '#2ecc71'],
        'laboratory'    => ['icon' => 'fa-microscope',        'label' => 'Laboratory',      'color' => '#a855f7'],
        'radiology'     => ['icon' => 'fa-x-ray',             'label' => 'Radiology',       'color' => '#f97316'],
        'pharmacy'      => ['icon' => 'fa-pills',             'label' => 'Pharmacy',        'color' => '#fbbf24'],
        'ipd'           => ['icon' => 'fa-bed-pulse',         'label' => 'IPD',             'color' => '#f85149'],
        'opd'           => ['icon' => 'fa-person-walking-arrow-right', 'label' => 'OPD',    'color' => '#06b6d4'],
        'billing'       => ['icon' => 'fa-file-invoice-dollar','label' => 'Billing',        'color' => '#10b981'],
        'hr'            => ['icon' => 'fa-users-gear',         'label' => 'HR',             'color' => '#8b5cf6'],
        'reports'       => ['icon' => 'fa-chart-bar',          'label' => 'Reports',        'color' => '#d97706'],
        'tasks'         => ['icon' => 'fa-list-check',        'label' => 'Tasks',          'color' => '#6366f1'],
        'inventory'     => ['icon' => 'fa-boxes-stacked',     'label' => 'Inventory',      'color' => '#ec4899'],
    ];

    public static array $designations = [
        'doctor'            => 'Doctor',
        'nurse'             => 'Nurse',
        'receptionist'      => 'Receptionist',
        'lab_technician'    => 'Lab Technician',
        'radiologist'       => 'Radiologist',
        'pharmacist'        => 'Pharmacist',
        'accountant'        => 'Accountant',
        'hr_officer'        => 'HR Officer',
        'data_entry'        => 'Data Entry',
        'it_staff'          => 'IT Staff',
        'admin_staff'       => 'Admin Staff',
        'employee'          => 'Employee',
    ];

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Access denied. Admin only.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%');
            });
        }
        if ($request->filled('designation')) {
            $query->where('designation', $request->designation);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('users.index', [
            'users'        => $users,
            'modules'      => self::$modules,
            'designations' => self::$designations,
            'departments'  => Department::where('is_active', true)->get(),
        ]);
    }

    public function create()
    {
        return view('users.create', [
            'modules'      => self::$modules,
            'designations' => self::$designations,
            'departments'  => Department::where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:191',
            'email'       => 'required|email|max:191|unique:users',
            'password'    => 'required|string|min:6|confirmed',
            'designation' => 'required|string',
            'phone'       => 'nullable|string|max:20',
            // Doctor specific
            'department_id'  => 'required_if:designation,doctor|nullable|exists:departments,id',
            'specialization' => 'required_if:designation,doctor|nullable|string|max:191',
            'fee'            => 'required_if:designation,doctor|nullable|numeric|min:0',
        ]);

        User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'role'           => 'user',
            'designation'    => $request->designation,
            'permissions'    => $request->permissions ?? [],
            'phone'          => $request->phone,
            'is_active'      => $request->boolean('is_active', true),
            'department_id'  => $request->department_id,
            'specialization' => $request->specialization,
            'qualification'  => $request->qualification,
            'fee'            => $request->fee ?? 0,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'user'         => $user,
            'modules'      => self::$modules,
            'designations' => self::$designations,
            'departments'  => Department::where('is_active', true)->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'        => 'required|string|max:191',
            'email'       => 'required|email|max:191|unique:users,email,'.$user->id,
            'designation' => 'required|string',
            'phone'       => 'nullable|string|max:20',
            'password'    => 'nullable|string|min:6|confirmed',
            // Doctor specific
            'department_id'  => 'required_if:designation,doctor|nullable|exists:departments,id',
            'specialization' => 'required_if:designation,doctor|nullable|string|max:191',
            'fee'            => 'required_if:designation,doctor|nullable|numeric|min:0',
        ]);

        $data = [
            'name'           => $request->name,
            'email'          => $request->email,
            'designation'    => $request->designation,
            'permissions'    => $request->permissions ?? [],
            'phone'          => $request->phone,
            'is_active'      => $request->boolean('is_active'),
            'department_id'  => $request->department_id,
            'specialization' => $request->specialization,
            'qualification'  => $request->qualification,
            'fee'            => $request->fee ?? 0,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'User status updated.');
    }
}
