<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogueItem extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'title', 'price', 'image_path', 'is_visible'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
