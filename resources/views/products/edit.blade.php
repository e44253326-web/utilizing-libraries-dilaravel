```blade
@extends('layouts.admin')

@section('title', 'Edit Produk | TOKO DIAN NAME')
@section('page-title', 'Edit Produk')

@section('content')
    <div class="container-fluid px-0">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h2 class="h4 fw-bold mb-1">Edit Produk</h2>
                <p class="text-muted mb-0">Perbarui informasi dan gambar produk.</p>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Produk
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                Periksa kembali data produk yang dimasukkan.
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('products._form')

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
```
