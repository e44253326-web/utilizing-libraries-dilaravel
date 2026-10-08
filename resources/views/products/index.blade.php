@extends('layouts.admin')

@section('title', 'Produk | TOKO DIAN NAME')
@section('page-title', 'Produk')

@section('content')
    <div class="container-fluid px-0">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h2 class="h4 fw-bold mb-1">Daftar Produk</h2>
                <p class="text-muted mb-0">Kelola produk yang tersedia di toko.</p>
            </div>
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Produk
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if ($product->image_path)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($product->image_path) }}" alt="Gambar {{ $product->name }}" width="56" height="56" class="rounded border" style="object-fit: cover">
                                    @else
                                        <span class="d-inline-flex align-items-center justify-content-center rounded border bg-light text-secondary" style="width: 56px; height: 56px" aria-label="Tidak ada gambar">
                                            <i class="bi bi-image"></i>
                                        </span>
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $product->name }}</td>
                                <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                <td>{{ $product->stock }}</td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-outline-info btn-sm">Detail</a>
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">Belum ada produk. Tambahkan produk pertama Anda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
