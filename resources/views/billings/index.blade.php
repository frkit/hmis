@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-receipt"></i> Billing</h2>
    <a href="{{ route('billings.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> New Bill
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Patient</th>
                    <th>Amount</th>
                    <th>Billing Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($billings as $billing)
                <tr>
                    <td>{{ $billing->id }}</td>
                    <td>{{ $billing->patient->name }}</td>
                    <td>${{ number_format($billing->amount, 2) }}</td>
                    <td>{{ $billing->billing_date->format('d M Y') }}</td>
                    <td>
                        <span class="badge bg-{{ $billing->status === 'paid' ? 'success' : ($billing->status === 'cancelled' ? 'danger' : 'warning') }}">
                            {{ ucfirst($billing->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('billings.show', $billing) }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('billings.edit', $billing) }}" class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('billings.destroy', $billing) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this bill?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-3">No billing records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $billings->links() }}</div>
@endsection
