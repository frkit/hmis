<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'title', 'description', 'module', 'priority',
        'status', 'due_date', 'assigned_to', 'assigned_by',
    ];

    protected function casts(): array
    {
        return ['due_date' => 'date'];
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public static array $priorities = [
        'low'    => ['label' => 'Low',    'color' => '#2ecc71'],
        'medium' => ['label' => 'Medium', 'color' => '#f0883e'],
        'high'   => ['label' => 'High',   'color' => '#f85149'],
        'urgent' => ['label' => 'Urgent', 'color' => '#a855f7'],
    ];

    public static array $statuses = [
        'pending'     => ['label' => 'Pending',     'color' => '#8b949e'],
        'in_progress' => ['label' => 'In Progress', 'color' => '#3b9eff'],
        'completed'   => ['label' => 'Completed',   'color' => '#2ecc71'],
        'cancelled'   => ['label' => 'Cancelled',   'color' => '#f85149'],
    ];

    public function isOverdue(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && !in_array($this->status, ['completed', 'cancelled']);
    }
}
