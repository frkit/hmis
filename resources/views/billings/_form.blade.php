<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Patient <span class="text-danger">*</span></label>
        <select name="patient_id" class="form-select @error('patient_id') is-invalid @enderror" required>
            <option value="">Select Patient</option>
            @foreach ($patients as $patient)
            <option value="{{ $patient->id }}" {{ old('patient_id', $billing->patient_id ?? '') == $patient->id ? 'selected' : '' }}>
                {{ $patient->name }}
            </option>
            @endforeach
        </select>
        @error('patient_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Appointment (optional)</label>
        <select name="appointment_id" class="form-select">
            <option value="">None</option>
            @foreach ($appointments as $appointment)
            <option value="{{ $appointment->id }}" {{ old('appointment_id', $billing->appointment_id ?? '') == $appointment->id ? 'selected' : '' }}>
                #{{ $appointment->id }} - {{ $appointment->patient->name }} ({{ $appointment->appointment_date->format('d M Y') }})
            </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Amount <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" name="amount" step="0.01" min="0" class="form-control @error('amount') is-invalid @enderror"
                   value="{{ old('amount', $billing->amount ?? '') }}" required>
        </div>
        @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Billing Date <span class="text-danger">*</span></label>
        <input type="date" name="billing_date" class="form-control @error('billing_date') is-invalid @enderror"
               value="{{ old('billing_date', isset($billing) ? $billing->billing_date->format('Y-m-d') : date('Y-m-d')) }}" required>
        @error('billing_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            @foreach (['pending', 'paid', 'cancelled'] as $s)
            <option value="{{ $s }}" {{ old('status', $billing->status ?? 'pending') === $s ? 'selected' : '' }}>
                {{ ucfirst($s) }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="2">{{ old('description', $billing->description ?? '') }}</textarea>
    </div>
</div>
<hr>
