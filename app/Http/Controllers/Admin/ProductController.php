<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\CatalogueItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $products = CatalogueItem::with(['category'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('admin/products/Index', [
            'products' => $products,
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/products/Create', [
            'categories' => Category::all(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048', // nullable for now
        ]);

        // 1. Handle Image
        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('catalogue', 'public');
        }

        // 2. Create Catalogue Item
        $catalogueItem = CatalogueItem::create([
            'title' => $validated['title'],
            'price' => $validated['price'],
            'category_id' => $validated['category_id'],
            'image_path' => $path,
            'is_visible' => true,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product created!');
    }

    public function edit(CatalogueItem $product)
    {
        return Inertia::render('admin/products/Edit', [
            'product' => $product->load('category'),
            'categories' => Category::all(['id', 'name']),
        ]);
    }

    public function update(Request $request, CatalogueItem $product)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'is_visible' => 'boolean',
        ]);

        // Update CatalogueItem
        $product->update([
            'title' => $validated['title'],
            'price' => $validated['price'],
            'category_id' => $validated['category_id'],
            'is_visible' => $validated['is_visible'] ?? true,
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $product->image_path = $request->file('image')->store('catalogue', 'public');
            $product->save();
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(CatalogueItem $product)
    {
        // Delete image
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->delete(); // cascade will delete inventory
        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }
}
