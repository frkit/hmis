@extends('layouts.app')

@section('content')
<h2 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard</h2>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card text-white bg-primary">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-people fs-1 me-3"></i>
                <div>
                    <div class="fs-2 fw-bold">{{ $stats['patients'] }}</div>
                    <div>Total Patients</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card text-white bg-success">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-person-badge fs-1 me-3"></i>
                <div>
                    <div class="fs-2 fw-bold">{{ $stats['doctors'] }}</div>
                    <div>Total Doctors</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card text-white bg-info">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-calendar3 fs-1 me-3"></i>
                <div>
                    <div class="fs-2 fw-bold">{{ $stats['appointments_today'] }}</div>
                    <div>Today's Appointments</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card text-white bg-warning">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-receipt fs-1 me-3"></i>
                <div>
                    <div class="fs-2 fw-bold">{{ $stats['pending_bills'] }}</div>
                    <div>Pending Bills</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white fw-bold">
        <i class="bi bi-clock-history"></i> Recent Appointments
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Date &amp; Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recent_appointments as $appointment)
                <tr>
                    <td>{{ $appointment->patient->name }}</td>
                    <td>{{ $appointment->doctor->name }}</td>
                    <td>{{ $appointment->appointment_date->format('d M Y, h:i A') }}</td>
                    <td>
                        <span class="badge bg-{{ $appointment->status === 'completed' ? 'success' : ($appointment->status === 'cancelled' ? 'danger' : 'primary') }}">
                            {{ ucfirst($appointment->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-3">No appointments found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
