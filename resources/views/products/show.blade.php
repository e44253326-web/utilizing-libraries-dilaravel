```blade
@extends('layouts.admin')

@section('title', 'Detail Produk | TOKO DIAN NAME')
@section('page-title', 'Detail Produk')

@section('content')
    <div class="container-fluid px-0">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <h2 class="h4 fw-bold mb-0">{{ $product->name }}</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Kembali</a>
                <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">Edit Produk</a>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                @if ($product->image_path)
                    <img
                        src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($product->image_path) }}"
                        alt="Gambar {{ $product->name }}"
                        class="img-fluid rounded border mb-4"
                        style="max-width: 320px; max-height: 320px; object-fit: contain"
                    >
                @endif
                <dl class="row mb-0">
                    <dt class="col-sm-3">Nama</dt>
                    <dd class="col-sm-9">{{ $product->name }}</dd>
                    <dt class="col-sm-3">Harga</dt>
                    <dd class="col-sm-9">Rp {{ number_format($product->price, 0, ',', '.') }}</dd>
                    <dt class="col-sm-3">Stok</dt>
                    <dd class="col-sm-9">{{ $product->stock }}</dd>
                    <dt class="col-sm-3">Dibuat</dt>
                    <dd class="col-sm-9">{{ $product->created_at->format('d-m-Y H:i') }}</dd>
                </dl>
            </div>
        </div>
    </div>
@endsection
```
