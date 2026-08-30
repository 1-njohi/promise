<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CatalogueItem;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = CatalogueItem::with(['category'])
            ->where('is_visible', true);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->sort === 'cheapest') {
            $query->orderBy('price', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12);

        return response()->json($products);
    }

    public function show($id)
    {
        $product = CatalogueItem::with(['category', 'inventory'])
            ->where('is_visible', true)
            ->findOrFail($id);

        return response()->json($product);
    }

    public function categories()
    {
        return response()->json(Category::all());
    }
}
