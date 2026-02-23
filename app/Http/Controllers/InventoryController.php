<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = InventoryItem::query();

        if ($request->filled('search'))   $query->where('name','like','%'.$request->search.'%');
        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->filled('stock')) {
            if ($request->stock === 'low')  $query->whereColumn('quantity', '<=', 'min_quantity');
            if ($request->stock === 'ok')   $query->whereColumn('quantity', '>',  'min_quantity');
        }

        $items = $query->orderBy('name')->paginate(20)->withQueryString();

        $stats = [
            'total'    => InventoryItem::count(),
            'low'      => InventoryItem::whereColumn('quantity','<=','min_quantity')->count(),
            'value'    => InventoryItem::selectRaw('SUM(quantity * unit_price) as v')->value('v') ?? 0,
            'expiring' => InventoryItem::whereNotNull('expiry_date')
                              ->where('expiry_date','<=', now()->addDays(30))
                              ->where('expiry_date','>=', now())->count(),
        ];

        return view('inventory.index', compact('items','stats'));
    }

    public function create()
    {
        return view('inventory.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:191',
            'category'     => 'required|string',
            'unit'         => 'required|string',
            'min_quantity' => 'required|numeric|min:0',
            'unit_price'   => 'required|numeric|min:0',
            'supplier'     => 'nullable|string|max:191',
            'location'     => 'nullable|string|max:191',
            'expiry_date'  => 'nullable|date',
            'barcode'      => 'nullable|string|max:100',
            'notes'        => 'nullable|string',
        ]);

        $item = InventoryItem::create([
            'name'         => $request->name,
            'category'     => $request->category,
            'unit'         => $request->unit,
            'quantity'     => 0,
            'min_quantity' => $request->min_quantity,
            'unit_price'   => $request->unit_price,
            'supplier'     => $request->supplier,
            'location'     => $request->location,
            'expiry_date'  => $request->expiry_date,
            'barcode'      => $request->barcode,
            'notes'        => $request->notes,
            'is_active'    => true,
        ]);

        // Initial stock if provided
        if ($request->filled('initial_qty') && $request->initial_qty > 0) {
            InventoryTransaction::create([
                'inventory_item_id' => $item->id,
                'type'              => 'in',
                'quantity'          => $request->initial_qty,
                'unit_price'        => $request->unit_price,
                'reference'         => 'Initial Stock',
                'notes'             => 'Opening stock',
                'created_by'        => auth()->id(),
            ]);
            $item->increment('quantity', $request->initial_qty);
        }

        return redirect()->route('inventory.index')->with('success', 'Item added to inventory.');
    }

    public function edit(InventoryItem $inventory)
    {
        $transactions = $inventory->transactions()
            ->with('creator')->latest()->take(10)->get();
        return view('inventory.edit', compact('inventory', 'transactions'));
    }

    public function update(Request $request, InventoryItem $inventory)
    {
        $request->validate([
            'name'         => 'required|string|max:191',
            'category'     => 'required|string',
            'unit'         => 'required|string',
            'min_quantity' => 'required|numeric|min:0',
            'unit_price'   => 'required|numeric|min:0',
        ]);

        $inventory->update($request->only([
            'name','category','unit','min_quantity','unit_price',
            'supplier','location','expiry_date','barcode','notes','is_active',
        ]));

        return redirect()->route('inventory.index')->with('success', 'Item updated successfully.');
    }

    public function destroy(InventoryItem $inventory)
    {
        $inventory->delete();
        return back()->with('success', 'Item removed from inventory.');
    }

    /** Stock In / Out */
    public function transaction(Request $request, InventoryItem $inventory)
    {
        $request->validate([
            'type'      => 'required|in:in,out,adjustment',
            'quantity'  => 'required|numeric|min:0.01',
            'reference' => 'nullable|string|max:191',
            'notes'     => 'nullable|string',
        ]);

        $qty = (float) $request->quantity;

        if ($request->type === 'out' && $inventory->quantity < $qty) {
            return back()->with('error', 'Insufficient stock. Available: '.$inventory->quantity.' '.$inventory->unit);
        }

        InventoryTransaction::create([
            'inventory_item_id' => $inventory->id,
            'type'              => $request->type,
            'quantity'          => $qty,
            'unit_price'        => $inventory->unit_price,
            'reference'         => $request->reference,
            'notes'             => $request->notes,
            'created_by'        => auth()->id(),
        ]);

        if ($request->type === 'in')  $inventory->increment('quantity', $qty);
        if ($request->type === 'out') $inventory->decrement('quantity', $qty);
        if ($request->type === 'adjustment') $inventory->update(['quantity' => $qty]);

        return back()->with('success', 'Stock transaction recorded.');
    }
}
