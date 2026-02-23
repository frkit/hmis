@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil-square"></i> Edit Bill</h2>
    <a href="{{ route('billings.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('billings.update', $billing) }}" method="POST">
            @csrf @method('PUT')
            @include('billings._form')
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update Bill</button>
        </form>
    </div>
</div>
@endsection
