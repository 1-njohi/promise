<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get all categories (with optional product counts)
     */
    public function index(Request $request)
    {
        $categories = Category::query();

        // Optional: Include product count
        if ($request->boolean('with_count')) {
            $categories->withCount('catalogueItems');
        }

        $categories->has('catalogueItems');

        // Sorting
        $sortBy = $request->input('sort_by', 'name');
        $sortOrder = $request->input('sort_order', 'asc');
        $categories->orderBy($sortBy, $sortOrder);

        // Pagination or all
        if ($request->boolean('paginate')) {
            $perPage = $request->input('per_page', 15);
            $categories = $categories->paginate($perPage);
        } else {
            $categories = $categories->get();
        }

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Get a single category with its products
     */
    public function show(Category $category)
    {
        $category->load('catalogueItems');

        return response()->json([
            'success' => true,
            'data' => $category,
        ]);
    }

    /**
     * Get categories with their products (for dropdowns)
     */
    public function dropdown()
    {
        $categories = Category::orderBy('name')
            ->get(['id', 'name', 'slug']);

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Get categories with product counts (for admin dashboard)
     */
    public function withCounts()
    {
        $categories = Category::withCount('catalogueItems')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }
}