@extends('layouts.dashboard')

@section('title', isset($inventory) ? 'Edit Item' : 'Add Item')
@section('page-title', isset($inventory) ? 'Edit Item' : 'Add Inventory Item')

@section('extra-styles')
<style>
.form-card { background:var(--surface); border:1px solid var(--border); border-radius:14px; overflow:hidden; margin-bottom:18px; }
.form-section-title { font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase;
    letter-spacing:1px; padding:14px 20px 10px; border-bottom:1px solid var(--border);
    display:flex; align-items:center; gap:8px; }
.form-body { padding:20px; }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px; }
.form-row-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; margin-bottom:14px; }
.form-group { display:flex; flex-direction:column; gap:5px; }
.form-group label { font-size:11px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.7px; }
.form-group input, .form-group select, .form-group textarea {
    background:var(--surface2); border:1px solid var(--border); color:var(--text);
    font-family:'Inter',sans-serif; font-size:13px; padding:9px 12px; border-radius:8px;
    outline:none; transition:border-color .2s; width:100%; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color:var(--accent); }
.btn-primary { display:inline-flex; align-items:center; gap:7px; padding:10px 20px; border-radius:9px;
    background:var(--accent); color:#fff; font-size:13px; font-weight:600; border:none; cursor:pointer;
    font-family:'Inter',sans-serif; text-decoration:none; transition:all .15s; }
.btn-primary:hover { background:var(--accent-dk); }
.btn-outline { display:inline-flex; align-items:center; gap:7px; padding:10px 20px; border-radius:9px;
    background:transparent; color:var(--text-muted); font-size:13px; border:1px solid var(--border);
    cursor:pointer; font-family:'Inter',sans-serif; text-decoration:none; }
.txn-row { display:flex; align-items:center; justify-content:space-between; padding:10px 0;
    border-bottom:1px solid var(--border); font-size:12px; }
.txn-row:last-child { border-bottom:none; }
@media(max-width:700px){ .form-row,.form-row-3{grid-template-columns:1fr;} }
</style>
@endsection

@section('content')

@php $isEdit = isset($inventory); @endphp

<div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
    <a href="{{ route('inventory.index') }}" style="color:var(--text-muted);text-decoration:none;font-size:13px;">
        <i class="fa-solid fa-arrow-left"></i> Back to Inventory
    </a>
    <i class="fa-solid fa-chevron-right" style="font-size:10px;color:var(--text-muted);"></i>
    <span style="font-size:13px;font-weight:600;">{{ $isEdit ? 'Edit: '.$inventory->name : 'Add New Item' }}</span>
</div>

<form method="POST" action="{{ $isEdit ? route('inventory.update',$inventory) : route('inventory.store') }}">
@csrf
@if($isEdit) @method('PUT') @endif

<!-- Basic Info -->
<div class="form-card">
    <div class="form-section-title"><i class="fa-solid fa-box"></i> Item Details</div>
    <div class="form-body">
        <div class="form-row">
            <div class="form-group">
                <label>Item Name *</label>
                <input type="text" name="name" value="{{ old('name',$inventory->name??'') }}"
                       placeholder="e.g. Amoxicillin 500mg" required>
            </div>
            <div class="form-group">
                <label>Barcode / SKU</label>
                <input type="text" name="barcode" value="{{ old('barcode',$inventory->barcode??'') }}"
                       placeholder="Optional barcode">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Category *</label>
                <select name="category" required>
                    @foreach(\App\Models\InventoryItem::$categories as $cat)
                    <option value="{{ $cat }}" {{ old('category',$inventory->category??'') == $cat ? 'selected':'' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Unit *</label>
                <select name="unit" required>
                    @foreach(\App\Models\InventoryItem::$units as $unit)
                    <option value="{{ $unit }}" {{ old('unit',$inventory->unit??'pcs') == $unit ? 'selected':'' }}>{{ $unit }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-row-3">
            <div class="form-group">
                <label>Unit Price (PKR) *</label>
                <input type="number" name="unit_price" step="0.01" min="0"
                       value="{{ old('unit_price',$inventory->unit_price??'') }}" required placeholder="0.00">
            </div>
            <div class="form-group">
                <label>Min Qty (Reorder) *</label>
                <input type="number" name="min_quantity" step="0.01" min="0"
                       value="{{ old('min_quantity',$inventory->min_quantity??'5') }}" required placeholder="5">
            </div>
            @if(!$isEdit)
            <div class="form-group">
                <label>Initial Stock Qty</label>
                <input type="number" name="initial_qty" step="0.01" min="0"
                       value="{{ old('initial_qty','0') }}" placeholder="0">
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Supply Info -->
<div class="form-card">
    <div class="form-section-title"><i class="fa-solid fa-truck"></i> Supply & Storage</div>
    <div class="form-body">
        <div class="form-row">
            <div class="form-group">
                <label>Supplier</label>
                <input type="text" name="supplier" value="{{ old('supplier',$inventory->supplier??'') }}"
                       placeholder="Supplier name">
            </div>
            <div class="form-group">
                <label>Storage Location</label>
                <input type="text" name="location" value="{{ old('location',$inventory->location??'') }}"
                       placeholder="e.g. Pharmacy Store, Ward 3">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Expiry Date</label>
                <input type="date" name="expiry_date"
                       value="{{ old('expiry_date', isset($inventory->expiry_date) ? $inventory->expiry_date->format('Y-m-d') : '') }}">
            </div>
            @if($isEdit)
            <div class="form-group" style="flex-direction:row;align-items:center;gap:10px;margin-top:18px;">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ old('is_active', $inventory->is_active??true) ? 'checked':'' }}
                       style="width:16px;height:16px;accent-color:var(--accent);">
                <label for="is_active" style="font-size:13px;color:var(--text);cursor:pointer;text-transform:none;letter-spacing:0;">
                    Item is Active
                </label>
            </div>
            @endif
        </div>
        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" rows="2" placeholder="Optional notes..." style="resize:vertical;">{{ old('notes',$inventory->notes??'') }}</textarea>
        </div>
    </div>
</div>

@if($isEdit && isset($transactions) && $transactions->count())
<!-- Recent Transactions -->
<div class="form-card">
    <div class="form-section-title"><i class="fa-solid fa-clock-rotate-left"></i> Recent Transactions</div>
    <div style="padding:8px 20px;">
        @foreach($transactions as $txn)
        <div class="txn-row">
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="width:22px;height:22px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:10px;
                    {{ $txn->type==='in' ? 'background:rgba(46,204,113,.12);color:#2ecc71;' : ($txn->type==='out' ? 'background:rgba(248,81,73,.12);color:#f85149;' : 'background:rgba(59,158,255,.12);color:#3b9eff;') }}">
                    <i class="fa-solid {{ $txn->type==='in' ? 'fa-arrow-down' : ($txn->type==='out' ? 'fa-arrow-up' : 'fa-sliders') }}"></i>
                </span>
                <div>
                    <span style="font-weight:600;text-transform:capitalize;">{{ $txn->type }}</span>
                    <span style="color:var(--text-muted);"> · {{ $txn->quantity }} {{ $inventory->unit }}</span>
                    @if($txn->reference)<span style="color:var(--text-muted);"> · {{ $txn->reference }}</span>@endif
                </div>
            </div>
            <div style="color:var(--text-muted);">{{ $txn->created_at->diffForHumans() }}</div>
        </div>
        @endforeach
    </div>
</div>
@endif

<div style="display:flex;gap:12px;">
    <button type="submit" class="btn-primary">
        <i class="fa-solid {{ $isEdit ? 'fa-floppy-disk' : 'fa-plus' }}"></i>
        {{ $isEdit ? 'Save Changes' : 'Add to Inventory' }}
    </button>
    <a href="{{ route('inventory.index') }}" class="btn-outline">Cancel</a>
</div>

</form>
@endsection
