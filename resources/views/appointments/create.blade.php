@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-calendar-plus"></i> New Appointment</h2>
    <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('appointments.store') }}" method="POST">
            @csrf
            @include('appointments._form')
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Appointment</button>
        </form>
    </div>
</div>
@endsection
