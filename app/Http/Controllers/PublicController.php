<?php

namespace App\Http\Controllers;

use App\Models\CatalogueItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicController extends Controller
{
    public function index(Request $request)
    {
        $query = CatalogueItem::with(['category'])
            ->where('is_visible', true);

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Sorting
        if ($request->sort === 'cheapest') {
            $query->orderBy('price', 'asc');
        } else {
            $query->orderBy('created_at', 'desc'); // Latest
        }

        $items = $query->paginate(12);

        return Inertia::render('Home', [
            'items' => $items,
            'categories' => Category::all(),
            'filters' => $request->only(['category', 'sort']),
        ]);
    }
}
