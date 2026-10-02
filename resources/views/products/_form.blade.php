<div class="mb-3">
    <label for="name" class="form-label">Nama produk</label>
    <input
        id="name"
        type="text"
        name="name"
        class="form-control @error('name') is-invalid @enderror"
        value="{{ old('name', $product->name ?? '') }}"
        maxlength="255"
        required
        autofocus
    >
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="price" class="form-label">Harga (Rp)</label>
    <input
        id="price"
        type="number"
        name="price"
        class="form-control @error('price') is-invalid @enderror"
        value="{{ old('price', $product->price ?? '') }}"
        min="0.01"
        step="0.01"
        required
    >
    @error('price')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div>
    <label for="stock" class="form-label">Stok</label>
    <input
        id="stock"
        type="number"
        name="stock"
        class="form-control @error('stock') is-invalid @enderror"
        value="{{ old('stock', $product->stock ?? 0) }}"
        min="0"
        step="1"
        required
    >
    @error('stock')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mt-3">
    <label for="image" class="form-label">Gambar produk (opsional)</label>
    <input
        id="image"
        type="file"
        name="image"
        class="form-control @error('image') is-invalid @enderror"
        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
    >
    <div class="form-text">Format JPG, PNG, atau WebP. Maksimal 2 MB.</div>
    @error('image')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@if (isset($product) && $product->image_path)
    <div class="mt-3">
        <p class="form-label">Gambar saat ini</p>
        <img
            src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($product->image_path) }}"
            alt="Gambar {{ $product->name }}"
            class="img-thumbnail"
            style="max-width: 180px; max-height: 180px; object-fit: contain"
        >
        <div class="form-check mt-2">
            <input id="remove_image" type="checkbox" name="remove_image" value="1" class="form-check-input">
            <label for="remove_image" class="form-check-label">Hapus gambar saat ini</label>
        </div>
    </div>
@endif
