@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-calendar3"></i> Appointment Details</h2>
    <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6"><strong>Patient:</strong> {{ $appointment->patient->name }}</div>
            <div class="col-md-6"><strong>Doctor:</strong> {{ $appointment->doctor->name }}</div>
            <div class="col-md-6"><strong>Date &amp; Time:</strong> {{ $appointment->appointment_date->format('d M Y, h:i A') }}</div>
            <div class="col-md-6"><strong>Status:</strong>
                <span class="badge bg-{{ $appointment->status === 'completed' ? 'success' : ($appointment->status === 'cancelled' ? 'danger' : 'primary') }}">
                    {{ ucfirst($appointment->status) }}
                </span>
            </div>
            <div class="col-12"><strong>Notes:</strong> {{ $appointment->notes ?? '-' }}</div>
        </div>
    </div>
</div>
@endsection
