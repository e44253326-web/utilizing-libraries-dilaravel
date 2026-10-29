<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use RuntimeException;

class ProductController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Product::class);

        $products = Product::latest()->get();

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        $this->authorize('create', Product::class);

        return view('products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Product::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'gt:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $imagePath = $request->hasFile('image')
            ? $this->storeProductImage($request->file('image'))
            : null;

        Product::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'image_path' => $imagePath,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan!');
    }

    public function show(Product $product): View
    {
        $this->authorize('view', $product);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $this->authorize('update', $product);

        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'gt:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
        ]);

        $oldImagePath = $product->image_path;
        $imagePath = $request->hasFile('image')
            ? $this->storeProductImage($request->file('image'))
            : ($request->boolean('remove_image') ? null : $oldImagePath);

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

    public function destroy(Product $product): RedirectResponse
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

    private function storeProductImage(UploadedFile $file): string
    {
        [$mediaType, $extension] = match ($file->getMimeType()) {
            'image/jpeg' => ['image/jpeg', 'jpg'],
            'image/png' => ['image/png', 'png'],
            'image/webp' => ['image/webp', 'webp'],
            default => throw new RuntimeException('Format gambar produk tidak didukung.'),
        };

        $image = (new ImageManager(new Driver))
            ->read($file->getRealPath())
            ->cover(800, 800);

        $encoded = match ($mediaType) {
            'image/jpeg' => $image->encodeUsingMediaType($mediaType, quality: 85),
            'image/webp' => $image->encodeUsingMediaType($mediaType, quality: 85),
            default => $image->encodeUsingMediaType($mediaType),
        };

        $path = 'products/'.Str::uuid().'.'.$extension;

        if (! Storage::disk('public')->put($path, $encoded->toString())) {
            throw new RuntimeException('Gambar produk gagal disimpan.');
        }

        return $path;
    }
}
