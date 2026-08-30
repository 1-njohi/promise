<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockReceiptItem extends Model
{
    use HasFactory;

    protected $fillable = ['stock_receipt_id', 'inventory_item_id', 'quantity', 'cost_price'];

    public function stockReceipt()
    {
        return $this->belongsTo(StockReceipt::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }
}