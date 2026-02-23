<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Full Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $doctor->name ?? '') }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Specialization <span class="text-danger">*</span></label>
        <input type="text" name="specialization" class="form-control @error('specialization') is-invalid @enderror"
               value="{{ old('specialization', $doctor->specialization ?? '') }}" required>
        @error('specialization') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $doctor->phone ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $doctor->email ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            @foreach (['active', 'inactive'] as $s)
            <option value="{{ $s }}" {{ old('status', $doctor->status ?? 'active') === $s ? 'selected' : '' }}>
                {{ ucfirst($s) }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Qualifications</label>
        <textarea name="qualifications" class="form-control" rows="2">{{ old('qualifications', $doctor->qualifications ?? '') }}</textarea>
    </div>
</div>
<hr>
