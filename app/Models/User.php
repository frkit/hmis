<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'role', 'designation', 'permissions',
        'phone', 'is_active',
        'department_id', 'specialization', 'qualification', 'fee', 'timings',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'permissions'       => 'array',
            'is_active'         => 'boolean',
            'timings'           => 'array',
            'fee'               => 'decimal:2',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /** Check if user has access to a specific module */
    public function hasPermission(string $module): bool
    {
        if ($this->isAdmin()) return true;
        return in_array($module, $this->permissions ?? []);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function getDesignationLabelAttribute(): string
    {
        $map = [
            'doctor'         => 'Doctor',
            'nurse'          => 'Nurse',
            'receptionist'   => 'Receptionist',
            'lab_technician' => 'Lab Technician',
            'radiologist'    => 'Radiologist',
            'pharmacist'     => 'Pharmacist',
            'accountant'     => 'Accountant',
            'hr_officer'     => 'HR Officer',
            'data_entry'     => 'Data Entry',
            'it_staff'       => 'IT Staff',
            'admin_staff'    => 'Admin Staff',
            'employee'       => 'Employee',
        ];
        return $map[$this->designation] ?? ucfirst($this->designation ?? 'Employee');
    }

    /** Helper to check if user is a doctor */
    public function isDoctor(): bool
    {
        return $this->designation === 'doctor';
    }
}
