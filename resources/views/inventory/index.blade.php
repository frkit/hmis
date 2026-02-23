@extends('layouts.dashboard')

@section('title', 'Inventory')
@section('page-title', 'Inventory')

@section('extra-styles')
<style>
.inv-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:20px; }
.inv-stat { background:var(--surface); border:1px solid var(--border); border-radius:12px;
    padding:16px 18px; display:flex; align-items:center; gap:12px; }
.inv-stat-icon { width:40px; height:40px; border-radius:10px; display:flex;
    align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
.inv-stat-val { font-size:22px; font-weight:700; line-height:1; }
.inv-stat-lbl { font-size:11px; color:var(--text-muted); margin-top:2px; }

.filter-bar { background:var(--surface); border:1px solid var(--border); border-radius:12px;
    padding:12px 16px; display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:18px; }
.filter-bar input, .filter-bar select {
    background:var(--surface2); border:1px solid var(--border); color:var(--text);
    font-family:'Inter',sans-serif; font-size:13px; padding:7px 11px; border-radius:8px; outline:none; }
.filter-bar input:focus, .filter-bar select:focus { border-color:var(--accent); }

.inv-wrap { background:var(--surface); border:1px solid var(--border); border-radius:14px; overflow:hidden; }
.inv-table { width:100%; border-collapse:collapse; }
.inv-table th { background:var(--surface2); padding:11px 15px; text-align:left; font-size:11px;
    font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px;
    border-bottom:1px solid var(--border); }
.inv-table td { padding:12px 15px; border-bottom:1px solid var(--border); font-size:13px; vertical-align:middle; }
.inv-table tr:last-child td { border-bottom:none; }
.inv-table tr:hover td { background:var(--surface2); }
.inv-table .low-row td { background:rgba(248,81,73,0.03) !important; }

.stock-bar-wrap { background:var(--surface2); border-radius:20px; height:6px; width:80px; overflow:hidden; }
.stock-bar { height:100%; border-radius:20px; }

.cat-badge { display:inline-flex; align-items:center; padding:2px 8px; border-radius:6px;
    font-size:11px; font-weight:600; background:var(--surface2); color:var(--text-muted); }

.act-btn { width:28px; height:28px; border-radius:6px; border:1px solid var(--border);
    display:inline-flex; align-items:center; justify-content:center;
    font-size:11px; cursor:pointer; text-decoration:none; color:var(--text-muted);
    background:var(--surface2); transition:all .15s; }
.act-btn:hover { color:var(--text); }
.act-btn.green:hover { color:#2ecc71; border-color:#2ecc71; }
.act-btn.red:hover { color:#f85149; border-color:#f85149; }

.btn-primary { display:inline-flex; align-items:center; gap:7px; padding:9px 18px; border-radius:9px;
    background:var(--accent); color:#fff; font-size:13px; font-weight:600; border:none; cursor:pointer;
    font-family:'Inter',sans-serif; text-decoration:none; transition:all .15s; }
.btn-primary:hover { background:var(--accent-dk); }

.alert-success { background:rgba(46,204,113,0.1); border:1px solid rgba(46,204,113,0.3); color:#2ecc71;
    padding:11px 15px; border-radius:9px; margin-bottom:16px; font-size:13px; }
.alert-error { background:rgba(248,81,73,0.1); border:1px solid rgba(248,81,73,.3); color:#f85149;
    padding:11px 15px; border-radius:9px; margin-bottom:16px; font-size:13px; }

/* Transaction modal */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6);
    z-index:200; align-items:center; justify-content:center; }
.modal-overlay.open { display:flex; }
.modal { background:var(--surface); border:1px solid var(--border); border-radius:14px;
    padding:24px; width:420px; max-width:95vw; }
.modal h3 { font-size:16px; font-weight:700; margin-bottom:16px; }
.modal .form-group { display:flex; flex-direction:column; gap:5px; margin-bottom:13px; }
.modal .form-group label { font-size:11px; font-weight:600; color:var(--text-muted); text-transform:uppercase; }
.modal .form-group input, .modal .form-group select, .modal .form-group textarea {
    background:var(--surface2); border:1px solid var(--border); color:var(--text);
    font-family:'Inter',sans-serif; font-size:13px; padding:9px 12px; border-radius:8px; outline:none; width:100%; }
.modal .form-group input:focus, .modal .form-group select:focus { border-color:var(--accent); }

@media(max-width:900px){ .inv-stats{grid-template-columns:repeat(2,1fr);} }
</style>
@endsection

@section('content')

@if(session('success'))
<div class="alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-error"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}</div>
@endif

<!-- Header -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
    <div>
        <h1 style="font-size:21px;font-weight:700;">Inventory Management</h1>
        <div style="font-size:13px;color:var(--text-muted);margin-top:2px;">Track stock, supplies, medicines and equipment</div>
    </div>
    <a href="{{ route('inventory.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> Add Item</a>
</div>

<!-- Stats -->
<div class="inv-stats">
    <div class="inv-stat">
        <div class="inv-stat-icon" style="background:rgba(59,158,255,0.12);color:#3b9eff;">
            <i class="fa-solid fa-boxes-stacked"></i></div>
        <div><div class="inv-stat-val">{{ $stats['total'] }}</div><div class="inv-stat-lbl">Total Items</div></div>
    </div>
    <div class="inv-stat">
        <div class="inv-stat-icon" style="background:rgba(248,81,73,0.12);color:#f85149;">
            <i class="fa-solid fa-triangle-exclamation"></i></div>
        <div><div class="inv-stat-val" style="color:#f85149;">{{ $stats['low'] }}</div><div class="inv-stat-lbl">Low Stock</div></div>
    </div>
    <div class="inv-stat">
        <div class="inv-stat-icon" style="background:rgba(46,204,113,0.12);color:#2ecc71;">
            <i class="fa-solid fa-coins"></i></div>
        <div><div class="inv-stat-val" style="color:#2ecc71;">{{ number_format($stats['value']) }}</div><div class="inv-stat-lbl">Total Value (PKR)</div></div>
    </div>
    <div class="inv-stat">
        <div class="inv-stat-icon" style="background:rgba(240,136,62,0.12);color:#f0883e;">
            <i class="fa-solid fa-calendar-xmark"></i></div>
        <div><div class="inv-stat-val" style="color:#f0883e;">{{ $stats['expiring'] }}</div><div class="inv-stat-lbl">Expiring in 30d</div></div>
    </div>
</div>

<!-- Filters -->
<form method="GET" class="filter-bar">
    <input type="text" name="search" placeholder="🔍 Search items..." value="{{ request('search') }}" style="min-width:180px;">
    <select name="category">
        <option value="">All Categories</option>
        @foreach(\App\Models\InventoryItem::$categories as $cat)
        <option value="{{ $cat }}" {{ request('category')==$cat?'selected':'' }}>{{ $cat }}</option>
        @endforeach
    </select>
    <select name="stock">
        <option value="">All Stock</option>
        <option value="low" {{ request('stock')=='low'?'selected':'' }}>Low Stock ⚠️</option>
        <option value="ok"  {{ request('stock')=='ok'?'selected':'' }}>In Stock ✅</option>
    </select>
    <button type="submit" class="btn-primary" style="padding:7px 14px;font-size:12px;">
        <i class="fa-solid fa-filter"></i> Filter
    </button>
    <a href="{{ route('inventory.index') }}" style="font-size:12px;color:var(--text-muted);text-decoration:none;padding:7px 10px;">
        <i class="fa-solid fa-xmark"></i> Clear
    </a>
</form>

<!-- Table -->
<div class="inv-wrap">
    @if($items->count())
    <table class="inv-table">
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Category</th>
                <th>Stock</th>
                <th>Min Qty</th>
                <th>Unit Price</th>
                <th>Supplier</th>
                <th>Expiry</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
        @php
            $pct = $item->min_quantity > 0 ? min(100, ($item->quantity / $item->min_quantity) * 100) : 100;
            $barColor = $item->isLowStock() ? '#f85149' : ($pct < 150 ? '#f0883e' : '#2ecc71');
        @endphp
        <tr class="{{ $item->isLowStock() ? 'low-row' : '' }}">
            <td>
                <div style="font-weight:600;">{{ $item->name }}</div>
                @if($item->location)<div style="font-size:11px;color:var(--text-muted);"><i class="fa-solid fa-location-dot" style="font-size:9px;"></i> {{ $item->location }}</div>@endif
            </td>
            <td><span class="cat-badge">{{ $item->category }}</span></td>
            <td>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div>
                        <span style="font-weight:700;font-size:14px;{{ $item->isLowStock() ? 'color:#f85149;' : '' }}">
                            {{ number_format($item->quantity,0) }}
                        </span>
                        <span style="font-size:11px;color:var(--text-muted);"> {{ $item->unit }}</span>
                    </div>
                    <div class="stock-bar-wrap">
                        <div class="stock-bar" style="width:{{ min(100,$pct) }}%;background:{{ $barColor }};"></div>
                    </div>
                    @if($item->isLowStock())
                    <i class="fa-solid fa-triangle-exclamation" style="color:#f85149;font-size:12px;" title="Low Stock"></i>
                    @endif
                </div>
            </td>
            <td style="font-size:12px;color:var(--text-muted);">{{ number_format($item->min_quantity,0) }} {{ $item->unit }}</td>
            <td style="font-weight:600;">{{ number_format($item->unit_price,2) }}</td>
            <td style="font-size:12px;color:var(--text-muted);">{{ $item->supplier ?? '—' }}</td>
            <td>
                @if($item->expiry_date)
                    <span style="{{ $item->isExpired() ? 'color:#f85149;font-weight:600;' : (($item->expiry_date->diffInDays(now()) < 30 && !$item->isExpired()) ? 'color:#f0883e;' : 'color:var(--text-muted);') }}">
                        {{ $item->expiry_date->format('M d, Y') }}
                    </span>
                @else
                    <span style="color:var(--text-muted);">—</span>
                @endif
            </td>
            <td>
                <div style="display:flex;gap:5px;">
                    <button type="button" class="act-btn green" title="Stock In"
                            onclick="openTxn({{ $item->id }}, '{{ addslashes($item->name) }}', 'in')">
                        <i class="fa-solid fa-arrow-down"></i>
                    </button>
                    <button type="button" class="act-btn red" title="Stock Out"
                            onclick="openTxn({{ $item->id }}, '{{ addslashes($item->name) }}', 'out')">
                        <i class="fa-solid fa-arrow-up"></i>
                    </button>
                    <a href="{{ route('inventory.edit',$item) }}" class="act-btn" title="Edit">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <form method="POST" action="{{ route('inventory.destroy',$item) }}"
                          onsubmit="return confirm('Delete {{ $item->name }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="act-btn red" title="Delete">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @if($items->hasPages())
    <div style="padding:12px 16px;border-top:1px solid var(--border);">
        {{ $items->links('pagination::simple-bootstrap-4') }}
    </div>
    @endif
    @else
    <div style="text-align:center;padding:60px 20px;color:var(--text-muted);">
        <i class="fa-solid fa-boxes-stacked" style="font-size:38px;margin-bottom:12px;display:block;"></i>
        <div style="font-size:15px;font-weight:600;margin-bottom:10px;">No items in inventory</div>
        <a href="{{ route('inventory.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> Add First Item</a>
    </div>
    @endif
</div>

<!-- Transaction Modal -->
<div class="modal-overlay" id="txnModal">
    <div class="modal">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <h3 id="txnTitle">Stock Transaction</h3>
            <button onclick="closeTxn()" style="background:none;border:none;color:var(--text-muted);font-size:18px;cursor:pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form method="POST" id="txnForm">
            @csrf
            <input type="hidden" name="type" id="txnType">
            <div class="form-group">
                <label>Item</label>
                <input type="text" id="txnItemName" readonly style="color:var(--text-muted);">
            </div>
            <div class="form-group">
                <label>Quantity *</label>
                <input type="number" name="quantity" min="0.01" step="0.01" placeholder="Enter quantity" required>
            </div>
            <div class="form-group">
                <label>Reference / Invoice No.</label>
                <input type="text" name="reference" placeholder="e.g. PO-2024-001">
            </div>
            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" rows="2" placeholder="Optional notes..." style="resize:none;"></textarea>
            </div>
            <div style="display:flex;gap:10px;margin-top:6px;">
                <button type="submit" class="btn-primary" id="txnSubmitBtn">
                    <i class="fa-solid fa-check"></i> Confirm
                </button>
                <button type="button" onclick="closeTxn()"
                        style="padding:9px 16px;border-radius:8px;border:1px solid var(--border);
                            background:transparent;color:var(--text-muted);font-size:13px;cursor:pointer;font-family:'Inter',sans-serif;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openTxn(id, name, type) {
    document.getElementById('txnModal').classList.add('open');
    document.getElementById('txnItemName').value = name;
    document.getElementById('txnType').value = type;
    document.getElementById('txnForm').action = '/inventory/' + id + '/transaction';
    document.getElementById('txnTitle').textContent = type === 'in' ? '📥 Stock In: ' + name : '📤 Stock Out: ' + name;
    document.getElementById('txnSubmitBtn').style.background = type === 'in' ? '#2ecc71' : '#f85149';
}
function closeTxn() {
    document.getElementById('txnModal').classList.remove('open');
}
document.getElementById('txnModal').addEventListener('click', function(e) {
    if (e.target === this) closeTxn();
});
</script>
@endsection
