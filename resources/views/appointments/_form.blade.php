<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Patient <span class="text-danger">*</span></label>
        <select name="patient_id" class="form-select @error('patient_id') is-invalid @enderror" required>
            <option value="">Select Patient</option>
            @foreach ($patients as $patient)
            <option value="{{ $patient->id }}" {{ old('patient_id', $appointment->patient_id ?? '') == $patient->id ? 'selected' : '' }}>
                {{ $patient->name }}
            </option>
            @endforeach
        </select>
        @error('patient_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Doctor <span class="text-danger">*</span></label>
        <select name="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror" required>
            <option value="">Select Doctor</option>
            @foreach ($doctors as $doctor)
            <option value="{{ $doctor->id }}" {{ old('doctor_id', $appointment->doctor_id ?? '') == $doctor->id ? 'selected' : '' }}>
                {{ $doctor->name }} ({{ $doctor->specialization }})
            </option>
            @endforeach
        </select>
        @error('doctor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Date &amp; Time <span class="text-danger">*</span></label>
        <input type="datetime-local" name="appointment_date" class="form-control @error('appointment_date') is-invalid @enderror"
               value="{{ old('appointment_date', isset($appointment) ? $appointment->appointment_date->format('Y-m-d\TH:i') : '') }}" required>
        @error('appointment_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            @foreach (['scheduled', 'completed', 'cancelled'] as $s)
            <option value="{{ $s }}" {{ old('status', $appointment->status ?? 'scheduled') === $s ? 'selected' : '' }}>
                {{ ucfirst($s) }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $appointment->notes ?? '') }}</textarea>
    </div>
</div>
<hr>
