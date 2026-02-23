@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil-square"></i> Edit Medicine</h2>
    <a href="{{ route('medicines.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('medicines.update', $medicine) }}" method="POST">
            @csrf @method('PUT')
            @include('medicines._form')
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update Medicine</button>
        </form>
    </div>
</div>
@endsection
