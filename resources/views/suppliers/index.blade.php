@extends('layouts.app')

@section('title', 'Pemasok / Supplier')
@section('page-title', 'Master Data Supplier')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Info -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-lg font-bold text-slate-800">Daftar Pemasok (Suppliers)</h1>
            <p class="text-xs text-slate-500">Data mitra distributor dan supplier untuk pengadaan stok barang masuk.</p>
        </div>
        <div>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('suppliers.create') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-md shadow-emerald-600/20 transition">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Supplier</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Search Card -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
        <form action="{{ route('suppliers.index') }}" method="GET" class="flex gap-2">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, telepon, atau email pemasok..."
                    class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold transition">
                Cari
            </button>
            @if($search)
                <a href="{{ route('suppliers.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-medium transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Suppliers Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="px-5 py-3.5">Nama Pemasok</th>
                        <th class="px-4 py-3.5">Kontak</th>
                        <th class="px-5 py-3.5">Alamat</th>
                        <th class="px-4 py-3.5 text-center">Riwayat Pembelian</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($suppliers as $supplier)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-bold text-slate-800 text-sm">
                                {{ $supplier->name }}
                            </td>
                            <td class="px-4 py-3.5 text-xs text-slate-600 space-y-1">
                                @if($supplier->phone)
                                    <div><i class="fa-solid fa-phone text-slate-400 mr-1.5"></i>{{ $supplier->phone }}</div>
                                @endif
                                @if($supplier->email)
                                    <div><i class="fa-solid fa-envelope text-slate-400 mr-1.5"></i>{{ $supplier->email }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-500">
                                {{ $supplier->address ?: '-' }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                    {{ $supplier->purchases_count }} transaksi
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1">
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('suppliers.edit', $supplier) }}" class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-medium transition">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 text-xs font-medium transition">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400 italic">Hanya Baca</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-400 text-sm">
                                Tidak ada supplier yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-slate-200/80 bg-slate-50/50">
            {{ $suppliers->links() }}
        </div>
    </div>

</div>
@endsection
