@extends('layouts.app')

@section('title', 'Tambah Supplier')
@section('page-title', 'Tambah Supplier')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <div class="border-b border-slate-100 pb-4 mb-6">
            <h1 class="text-xl font-bold text-slate-800">Tambah Pemasok Baru</h1>
            <p class="text-xs text-slate-500">Lengkapi data pemasok / supplier untuk transaksi pembelian stok.</p>
        </div>

        <form action="{{ route('suppliers.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1 uppercase tracking-wider">Nama Perusahaan / Supplier <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: PT Sinar Mandiri Distribusi"
                    class="w-full px-3 py-2.5 rounded-xl border @error('name') border-red-400 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="phone" class="block text-xs font-semibold text-slate-700 mb-1 uppercase tracking-wider">Nomor Telepon / HP</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="081234567890"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1 uppercase tracking-wider">Alamat Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="kontak@perusahaan.com"
                        class="w-full px-3 py-2.5 rounded-xl border @error('email') border-red-400 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="address" class="block text-xs font-semibold text-slate-700 mb-1 uppercase tracking-wider">Alamat Kantor / Gudang</label>
                <textarea name="address" id="address" rows="3" placeholder="Alamat lengkap supplier..."
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">{{ old('address') }}</textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-5 border-t border-slate-100">
                <a href="{{ route('suppliers.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50 text-sm font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-md shadow-emerald-600/30 transition">
                    Simpan Supplier
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
