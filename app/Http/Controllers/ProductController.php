<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ProductController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Product::class);

        $products = Product::latest()->get();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $this->authorize('create', Product::class);

        return view('products.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Product::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|gt:0',
            'stock' => 'required|integer|min:0',
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $imagePath = $request->file('image')?->storePublicly('products', 'public');
        if ($request->hasFile('image') && $imagePath === false) {
            throw new RuntimeException('Gambar produk gagal disimpan.');
        }

        Product::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'image_path' => $imagePath,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan!');
    }

    public function show(Product $product)
    {
        $this->authorize('view', $product);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);

        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|gt:0',
            'stock' => 'required|integer|min:0',
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
        ]);

        $oldImagePath = $product->image_path;
        $newImagePath = $request->file('image')?->storePublicly('products', 'public');
        if ($request->hasFile('image') && $newImagePath === false) {
            throw new RuntimeException('Gambar produk gagal disimpan.');
        }

        $imagePath = $newImagePath ?: ($request->boolean('remove_image') ? null : $oldImagePath);
        $product->update([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'image_path' => $imagePath,
        ]);

        if ($oldImagePath && $imagePath !== $oldImagePath) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diubah!');
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        $imagePath = $product->image_path;
        $product->delete();

        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus!');
    }
}
