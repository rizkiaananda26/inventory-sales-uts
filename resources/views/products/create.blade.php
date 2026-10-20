@extends('layouts.app')

@section('title', 'Tambah Produk Baru')
@section('page-title', 'Tambah Produk Baru')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <div class="border-b border-slate-100 pb-4 mb-6">
            <h1 class="text-xl font-bold text-slate-800">Formulir Tambah Produk</h1>
            <p class="text-xs text-slate-500">Lengkapi data produk, SKU unik, harga, dan kontrol stok minimum.</p>
        </div>

        <form action="{{ route('products.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="sku" class="block text-xs font-semibold text-slate-700 mb-1 uppercase tracking-wider">SKU Produk <span class="text-red-500">*</span></label>
                    <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required placeholder="Contoh: PRD-009"
                        class="w-full px-3 py-2.5 rounded-xl border @error('sku') border-red-400 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    @error('sku')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-xs font-semibold text-slate-700 mb-1 uppercase tracking-wider">Kategori <span class="text-red-500">*</span></label>
                    <select name="category_id" id="category_id" required
                        class="w-full px-3 py-2.5 rounded-xl border @error('category_id') border-red-400 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1 uppercase tracking-wider">Nama Produk Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Flashdisk Sandisk 64GB USB 3.0"
                    class="w-full px-3 py-2.5 rounded-xl border @error('name') border-red-400 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="purchase_price" class="block text-xs font-semibold text-slate-700 mb-1 uppercase tracking-wider">Harga Beli / Pokok (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="purchase_price" id="purchase_price" value="{{ old('purchase_price') }}" required placeholder="Contoh: 75000"
                        class="w-full px-3 py-2.5 rounded-xl border @error('purchase_price') border-red-400 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    @error('purchase_price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="selling_price" class="block text-xs font-semibold text-slate-700 mb-1 uppercase tracking-wider">Harga Jual (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="selling_price" id="selling_price" value="{{ old('selling_price') }}" required placeholder="Contoh: 95000"
                        class="w-full px-3 py-2.5 rounded-xl border @error('selling_price') border-red-400 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    @error('selling_price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="stock" class="block text-xs font-semibold text-slate-700 mb-1 uppercase tracking-wider">Stok Awal Fisik <span class="text-red-500">*</span></label>
                    <input type="number" min="0" name="stock" id="stock" value="{{ old('stock', 0) }}" required
                        class="w-full px-3 py-2.5 rounded-xl border @error('stock') border-red-400 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    @error('stock')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="minimum_stock" class="block text-xs font-semibold text-slate-700 mb-1 uppercase tracking-wider">Batas Minimum Stok <span class="text-red-500">*</span></label>
                    <input type="number" min="0" name="minimum_stock" id="minimum_stock" value="{{ old('minimum_stock', 5) }}" required
                        class="w-full px-3 py-2.5 rounded-xl border @error('minimum_stock') border-red-400 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <p class="text-[11px] text-slate-400 mt-1">Sistem akan memberi alert saat stok mencapai atau di bawah batas ini.</p>
                    @error('minimum_stock')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50 text-sm font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-md shadow-emerald-600/30 transition">
                    <i class="fa-solid fa-save mr-1.5"></i> Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
