@extends('layouts.app')

@section('title', 'Penjualan')
@section('page-title', 'Transaksi Penjualan (Kasir / Stok Keluar)')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Info -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-lg font-bold text-slate-800">Riwayat Penjualan (Stok Keluar)</h1>
            <p class="text-xs text-slate-500">Pencatatan penjualan ke pelanggan. Stok berkurang otomatis secara atomik setelah transaksi sukses.</p>
        </div>
        <div>
            <a href="{{ route('sales.create') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-md shadow-emerald-600/20 transition">
                <i class="fa-solid fa-cart-plus"></i>
                <span>Catat Penjualan Baru</span>
            </a>
        </div>
    </div>

    <!-- Filter Card (Slide 18) -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
        <form action="{{ route('sales.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <div class="sm:col-span-4">
                <label for="search" class="block text-xs font-semibold text-slate-600 mb-1">Cari (No. Invoice / Pelanggan)</label>
                <input type="text" name="search" id="search" value="{{ $search }}" placeholder="INV-2026... atau nama pelanggan"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div class="sm:col-span-2">
                <label for="status" class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                <select name="status" id="status" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <label for="start_date" class="block text-xs font-semibold text-slate-600 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div class="sm:col-span-2">
                <label for="end_date" class="block text-xs font-semibold text-slate-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div class="sm:col-span-2 flex items-center space-x-2">
                <button type="submit" class="w-full px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold transition">
                    Filter
                </button>
                @if($search || $status || $startDate || $endDate)
                    <a href="{{ route('sales.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-medium transition" title="Reset filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Sales Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="px-5 py-3.5">Nomor Invoice</th>
                        <th class="px-5 py-3.5">Pelanggan</th>
                        <th class="px-4 py-3.5">Tanggal</th>
                        <th class="px-4 py-3.5 text-center">Item</th>
                        <th class="px-4 py-3.5 text-right">Total Belanja</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                        <th class="px-4 py-3.5">Kasir / Staff</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-mono text-xs font-bold text-slate-800">
                                <a href="{{ route('sales.show', $sale) }}" class="hover:text-emerald-600 transition">
                                    {{ $sale->sale_number }}
                                </a>
                            </td>
                            <td class="px-5 py-3.5 text-xs font-medium text-slate-700">
                                {{ $sale->customer_name }}
                            </td>
                            <td class="px-4 py-3.5 text-xs text-slate-500">
                                {{ $sale->sold_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs bg-slate-100 text-slate-600 font-medium">
                                    {{ $sale->items->count() }} produk
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right text-xs font-bold text-slate-800">
                                Rp {{ number_format($sale->total, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if($sale->isCompleted())
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        Selesai
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                        Dibatalkan
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-xs text-slate-500">
                                {{ $sale->user->name }}
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('sales.show', $sale) }}" class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium transition">
                                    <i class="fa-solid fa-receipt"></i>
                                    <span>Struk</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-slate-400 text-sm">
                                Tidak ada transaksi penjualan yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-slate-200/80 bg-slate-50/50">
            {{ $sales->links() }}
        </div>
    </div>

</div>
@endsection
