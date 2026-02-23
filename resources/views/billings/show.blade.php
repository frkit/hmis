@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-receipt"></i> Bill Details</h2>
    <a href="{{ route('billings.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6"><strong>Patient:</strong> {{ $billing->patient->name }}</div>
            <div class="col-md-6"><strong>Appointment:</strong>
                @if ($billing->appointment)
                    #{{ $billing->appointment->id }} - {{ $billing->appointment->appointment_date->format('d M Y') }}
                @else
                    -
                @endif
            </div>
            <div class="col-md-4"><strong>Amount:</strong> ${{ number_format($billing->amount, 2) }}</div>
            <div class="col-md-4"><strong>Billing Date:</strong> {{ $billing->billing_date->format('d M Y') }}</div>
            <div class="col-md-4"><strong>Status:</strong>
                <span class="badge bg-{{ $billing->status === 'paid' ? 'success' : ($billing->status === 'cancelled' ? 'danger' : 'warning') }}">
                    {{ ucfirst($billing->status) }}
                </span>
            </div>
            <div class="col-12"><strong>Description:</strong> {{ $billing->description ?? '-' }}</div>
        </div>
    </div>
</div>
@endsection
