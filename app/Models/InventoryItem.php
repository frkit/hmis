<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    protected $fillable = [
        'name', 'category', 'unit', 'quantity', 'min_quantity',
        'unit_price', 'supplier', 'location', 'expiry_date',
        'barcode', 'notes', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'expiry_date' => 'date',
            'is_active'   => 'boolean',
            'quantity'    => 'decimal:2',
            'min_quantity'=> 'decimal:2',
            'unit_price'  => 'decimal:2',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->min_quantity;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public static array $categories = [
        'Medicine', 'Equipment', 'Surgical Supply',
        'Consumable', 'Lab Reagent', 'Linen', 'Stationery', 'Other',
    ];

    public static array $units = [
        'pcs', 'box', 'bottle', 'strip', 'vial', 'kg', 'g', 'L', 'mL', 'pack', 'roll',
    ];
}
