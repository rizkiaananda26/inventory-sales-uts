@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru')
@section('page-title', 'Tambah Pengguna Baru')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <div class="border-b border-slate-100 pb-4 mb-6">
            <h1 class="text-xl font-bold text-slate-800">Tambah Akun Pengguna</h1>
            <p class="text-xs text-slate-500">Buat akun untuk staf atau administrator baru dengan role yang sesuai.</p>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1 uppercase tracking-wider">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Budi Santoso"
                    class="w-full px-3 py-2.5 rounded-xl border @error('name') border-red-400 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1 uppercase tracking-wider">Alamat Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="budi@inventory.test"
                    class="w-full px-3 py-2.5 rounded-xl border @error('email') border-red-400 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 mb-1 uppercase tracking-wider">Password <span class="text-red-500">*</span></label>
                <input type="password" name="password" id="password" required placeholder="Minimal 6 karakter"
                    class="w-full px-3 py-2.5 rounded-xl border @error('password') border-red-400 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="role" class="block text-xs font-semibold text-slate-700 mb-1 uppercase tracking-wider">Role / Hak Akses <span class="text-red-500">*</span></label>
                <select name="role" id="role" required
                    class="w-full px-3 py-2.5 rounded-xl border @error('role') border-red-400 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff (Pencatatan Penjualan &amp; Pembelian, Baca Master)</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin / Owner (Akses Penuh Master, Kelola User, Pembatalan)</option>
                </select>
                @error('role')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end space-x-3 pt-5 border-t border-slate-100">
                <a href="{{ route('users.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50 text-sm font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-md shadow-emerald-600/30 transition">
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
