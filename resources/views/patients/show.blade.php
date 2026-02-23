@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-person"></i> Patient Details</h2>
    <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6"><strong>Name:</strong> {{ $patient->name }}</div>
            <div class="col-md-3"><strong>Date of Birth:</strong> {{ $patient->date_of_birth }}</div>
            <div class="col-md-3"><strong>Gender:</strong> {{ ucfirst($patient->gender) }}</div>
            <div class="col-md-4"><strong>Phone:</strong> {{ $patient->phone ?? '-' }}</div>
            <div class="col-md-4"><strong>Email:</strong> {{ $patient->email ?? '-' }}</div>
            <div class="col-md-4"><strong>Blood Group:</strong> {{ $patient->blood_group ?? '-' }}</div>
            <div class="col-md-6"><strong>Address:</strong> {{ $patient->address ?? '-' }}</div>
            <div class="col-md-6"><strong>Medical History:</strong> {{ $patient->medical_history ?? '-' }}</div>
        </div>
    </div>
</div>

<h5>Appointments</h5>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Date</th><th>Doctor</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse ($patient->appointments as $appt)
                <tr>
                    <td>{{ $appt->appointment_date->format('d M Y, h:i A') }}</td>
                    <td>{{ $appt->doctor->name }}</td>
                    <td><span class="badge bg-{{ $appt->status === 'completed' ? 'success' : ($appt->status === 'cancelled' ? 'danger' : 'primary') }}">{{ ucfirst($appt->status) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center text-muted py-3">No appointments.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
