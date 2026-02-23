@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-capsule"></i> Medicine Details</h2>
    <a href="{{ route('medicines.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6"><strong>Name:</strong> {{ $medicine->name }}</div>
            <div class="col-md-6"><strong>Generic Name:</strong> {{ $medicine->generic_name ?? '-' }}</div>
            <div class="col-md-6"><strong>Manufacturer:</strong> {{ $medicine->manufacturer ?? '-' }}</div>
            <div class="col-md-6"><strong>Expiry Date:</strong>
                {{ $medicine->expiry_date ? $medicine->expiry_date->format('d M Y') : '-' }}
            </div>
            <div class="col-md-4"><strong>Stock:</strong>
                {{ $medicine->stock }}
                @if ($medicine->stock < 10)
                    <span class="badge bg-danger ms-1">Low Stock</span>
                @endif
            </div>
            <div class="col-md-4"><strong>Unit Price:</strong> ${{ number_format($medicine->unit_price, 2) }}</div>
        </div>
    </div>
</div>
@endsection
