@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil-square"></i> Edit Doctor</h2>
    <a href="{{ route('doctors.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('doctors.update', $doctor) }}" method="POST">
            @csrf @method('PUT')
            @include('doctors._form')
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update Doctor</button>
        </form>
    </div>
</div>
@endsection
