<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Medicine Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $medicine->name ?? '') }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Generic Name</label>
        <input type="text" name="generic_name" class="form-control"
               value="{{ old('generic_name', $medicine->generic_name ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Manufacturer</label>
        <input type="text" name="manufacturer" class="form-control"
               value="{{ old('manufacturer', $medicine->manufacturer ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Expiry Date</label>
        <input type="date" name="expiry_date" class="form-control"
               value="{{ old('expiry_date', isset($medicine) && $medicine->expiry_date ? $medicine->expiry_date->format('Y-m-d') : '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Stock <span class="text-danger">*</span></label>
        <input type="number" name="stock" min="0" class="form-control @error('stock') is-invalid @enderror"
               value="{{ old('stock', $medicine->stock ?? 0) }}" required>
        @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Unit Price <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" name="unit_price" step="0.01" min="0" class="form-control @error('unit_price') is-invalid @enderror"
                   value="{{ old('unit_price', $medicine->unit_price ?? '') }}" required>
        </div>
        @error('unit_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
<hr>
