@extends('layouts.app')

@section('title', 'Daftar Produk')
@section('page-title', 'Master Data Produk')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Filter Header -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
        <div>
            <h1 class="text-lg font-bold text-slate-800">Katalog Produk &amp; Stok</h1>
            <p class="text-xs text-slate-500">Kelola informasi produk, SKU, kategori, harga, dan kontrol batas stok.</p>
        </div>
        <div class="flex items-center space-x-3">
            @if(auth()->user()->isAdmin())
                <a href="{{ route('products.create') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-md shadow-emerald-600/20 transition">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Produk Baru</span>
                </a>
            @else
                <span class="text-xs text-slate-500 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                    <i class="fa-solid fa-info-circle mr-1"></i> Mode Baca (Staff)
                </span>
            @endif
        </div>
    </div>

    <!-- Search & Filter Card (Slide 18) -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
        <form action="{{ route('products.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <div class="sm:col-span-5">
                <label for="search" class="block text-xs font-semibold text-slate-600 mb-1">Cari Produk (SKU / Nama)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </div>
                    <input type="text" name="search" id="search" value="{{ $search }}"
                        placeholder="Contoh: PRD-001 atau Logitech..."
                        class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
            </div>

            <div class="sm:col-span-4">
                <label for="category_id" class="block text-xs font-semibold text-slate-600 mb-1">Kategori</label>
                <select name="category_id" id="category_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-3 flex items-center space-x-2">
                <label class="flex items-center space-x-2 text-xs text-slate-700 cursor-pointer py-2">
                    <input type="checkbox" name="low_stock" value="1" {{ $lowStock ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                    <span class="font-medium text-amber-700">Hanya Stok Kritis</span>
                </label>
                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold transition ml-auto">
                    Filter
                </button>
                @if($search || $categoryId || $lowStock)
                    <a href="{{ route('products.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-medium transition" title="Reset filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="px-5 py-3.5">SKU</th>
                        <th class="px-5 py-3.5">Nama Produk</th>
                        <th class="px-4 py-3.5">Kategori</th>
                        <th class="px-4 py-3.5 text-right">Harga Beli</th>
                        <th class="px-4 py-3.5 text-right">Harga Jual</th>
                        <th class="px-4 py-3.5 text-center">Stok Fisik</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-mono text-xs font-bold text-slate-700">
                                {{ $product->sku }}
                            </td>
                            <td class="px-5 py-3.5">
                                <a href="{{ route('products.show', $product) }}" class="font-semibold text-slate-800 hover:text-emerald-600 transition">
                                    {{ $product->name }}
                                </a>
                            </td>
                            <td class="px-4 py-3.5 text-xs">
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 font-medium border border-slate-200">
                                    {{ $product->category->name }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right text-xs text-slate-600">
                                Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5 text-right text-xs font-semibold text-slate-800">
                                Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if($product->isLowStock())
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700" title="Stok berada di bawah minimum ({{ $product->minimum_stock }})">
                                        <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                        <span>{{ $product->stock }} (Min: {{ $product->minimum_stock }})</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        {{ $product->stock }} unit
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1">
                                <a href="{{ route('products.show', $product) }}" class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium transition" title="Lihat Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-medium transition" title="Edit Produk">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 text-xs font-medium transition" title="Hapus Produk">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-slate-400 text-sm">
                                <i class="fa-solid fa-inbox text-3xl mb-2 block"></i>
                                Tidak ada produk yang sesuai dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-5 py-4 border-t border-slate-200/80 bg-slate-50/50">
            {{ $products->links() }}
        </div>
    </div>

</div>
@endsection
