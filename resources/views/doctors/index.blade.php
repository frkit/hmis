@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-person-badge"></i> Doctors</h2>
    <a href="{{ route('doctors.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Doctor
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Specialization</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($doctors as $doctor)
                <tr>
                    <td>{{ $doctor->id }}</td>
                    <td>{{ $doctor->name }}</td>
                    <td>{{ $doctor->specialization }}</td>
                    <td>{{ $doctor->phone }}</td>
                    <td>{{ $doctor->email }}</td>
                    <td>
                        <span class="badge bg-{{ $doctor->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($doctor->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('doctors.show', $doctor) }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('doctors.edit', $doctor) }}" class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('doctors.destroy', $doctor) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this doctor?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-3">No doctors found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $doctors->links() }}</div>
@endsection
