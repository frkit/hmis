<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Full Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $patient->name ?? '') }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
        <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror"
               value="{{ old('date_of_birth', isset($patient) ? $patient->date_of_birth : '') }}" required>
        @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Gender <span class="text-danger">*</span></label>
        <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
            <option value="">Select</option>
            @foreach (['male', 'female', 'other'] as $g)
            <option value="{{ $g }}" {{ old('gender', $patient->gender ?? '') === $g ? 'selected' : '' }}>
                {{ ucfirst($g) }}
            </option>
            @endforeach
        </select>
        @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $patient->phone ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $patient->email ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Blood Group</label>
        <select name="blood_group" class="form-select">
            <option value="">Select</option>
            @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
            <option value="{{ $bg }}" {{ old('blood_group', $patient->blood_group ?? '') === $bg ? 'selected' : '' }}>
                {{ $bg }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Address</label>
        <textarea name="address" class="form-control" rows="2">{{ old('address', $patient->address ?? '') }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">Medical History</label>
        <textarea name="medical_history" class="form-control" rows="2">{{ old('medical_history', $patient->medical_history ?? '') }}</textarea>
    </div>
</div>
<hr>
