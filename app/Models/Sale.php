<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = ['sale_date', 'reference', 'notes', 'total_items', 'total_quantity', 'total_amount'];

    protected $casts = [
        'sale_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function inventoryItems()
    {
        return $this->belongsToMany(InventoryItem::class, 'sale_items')
                    ->withPivot('quantity', 'total_price', 'unit_price', 'cost_price', 'profit');
    }
}
