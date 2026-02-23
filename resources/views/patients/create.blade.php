@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-person-plus"></i> Add Patient</h2>
    <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('patients.store') }}" method="POST">
            @csrf
            @include('patients._form')
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Save Patient
            </button>
        </form>
    </div>
</div>
@endsection
