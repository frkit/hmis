@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-capsule"></i> Pharmacy / Medicines</h2>
    <a href="{{ route('medicines.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Medicine
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Generic Name</th>
                    <th>Manufacturer</th>
                    <th>Stock</th>
                    <th>Unit Price</th>
                    <th>Expiry</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($medicines as $medicine)
                <tr class="{{ $medicine->stock < 10 ? 'table-warning' : '' }}">
                    <td>{{ $medicine->id }}</td>
                    <td>{{ $medicine->name }}</td>
                    <td>{{ $medicine->generic_name ?? '-' }}</td>
                    <td>{{ $medicine->manufacturer ?? '-' }}</td>
                    <td>
                        {{ $medicine->stock }}
                        @if ($medicine->stock < 10)
                            <span class="badge bg-danger ms-1">Low</span>
                        @endif
                    </td>
                    <td>${{ number_format($medicine->unit_price, 2) }}</td>
                    <td>{{ $medicine->expiry_date ? $medicine->expiry_date->format('d M Y') : '-' }}</td>
                    <td>
                        <a href="{{ route('medicines.show', $medicine) }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('medicines.edit', $medicine) }}" class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('medicines.destroy', $medicine) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this medicine?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-3">No medicines found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $medicines->links() }}</div>
@endsection
