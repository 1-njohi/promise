<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'sku', 'quantity', 'cost_price', 'reorder_level', 'location'];

    public function stockReceiptItems()
    {
        return $this->hasMany(StockReceiptItem::class);
    }
}