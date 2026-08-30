<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockReceipt extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_id', 'receipt_date', 'notes'];

    protected $casts = [
        'receipt_date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(StockReceiptItem::class);
    }

    public function catalogueItems()
    {
        return $this->belongsToMany(CatalogueItem::class, 'stock_receipt_items')
                    ->withPivot('quantity', 'cost_price');
    }

    // Revert stock when deleting receipt
    public function revertStock()
    {
        foreach ($this->items as $item) {
            $inventory = InventoryItem::where('catalogue_item_id', $item->catalogue_item_id)->first();
            if ($inventory) {
                $newQty = $inventory->quantity - $item->quantity;
                if ($newQty < 0) {
                    // Optionally log or handle, but for safety we can set to 0
                    $newQty = 0;
                }
                $inventory->quantity = $newQty;
                $inventory->save();
            }
        }
    }
}