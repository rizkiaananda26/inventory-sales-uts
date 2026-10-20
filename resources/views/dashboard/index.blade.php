@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Ringkasan & Dashboard Utama')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar & Greetings -->
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-semibold mb-2 border border-emerald-500/30">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Sistem Operasional Aktif</span>
            </div>
            <h1 class="text-2xl font-bold">Halo, {{ auth()->user()->name }}!</h1>
            <p class="text-sm text-slate-300 mt-1">Selamat datang di sistem manajemen stok dan transaksi penjualan.</p>
        </div>
        <div class="flex flex-wrap gap-2.5">
            <a href="{{ route('sales.create') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-semibold text-sm shadow-lg shadow-emerald-500/30 transition">
                <i class="fa-solid fa-cart-plus"></i>
                <span>Catat Penjualan (Kasir)</span>
            </a>
            <a href="{{ route('purchases.create') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-slate-700 hover:bg-slate-600 text-white font-semibold text-sm transition">
                <i class="fa-solid fa-dolly"></i>
                <span>Restok Pembelian</span>
            </a>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('products.create') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Produk</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Aggregate Metric Cards (Slide 18) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total Products -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Jenis Produk</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($totalProducts) }}</h3>
                <p class="text-xs text-slate-500 mt-1">{{ number_format($totalStockUnits) }} total unit fisik</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
        </div>

        <!-- Low Stock Warning (Slide 6 & 18) -->
        <div class="bg-white rounded-2xl p-5 border {{ $lowStockCount > 0 ? 'border-amber-300 bg-amber-50/20' : 'border-slate-200/80' }} shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Peringatan Stok Rendah</p>
                <h3 class="text-2xl font-black {{ $lowStockCount > 0 ? 'text-amber-600' : 'text-slate-800' }} mt-1">
                    {{ number_format($lowStockCount) }}
                </h3>
                <p class="text-xs text-slate-500 mt-1">
                    @if($lowStockCount > 0)
                        <span class="text-amber-600 font-medium">Perlu restok segera!</span>
                    @else
                        Stok dalam batas aman
                    @endif
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl {{ $lowStockCount > 0 ? 'bg-amber-100 text-amber-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>

        <!-- Sales Today -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Penjualan Hari Ini</p>
                <h3 class="text-xl font-black text-emerald-600 mt-1">Rp {{ number_format($salesToday, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-500 mt-1">Bulan ini: Rp {{ number_format($salesThisMonth, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-wallet"></i>
            </div>
        </div>

        <!-- Total Transactions -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Transaksi Selesai</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($totalSales + $totalPurchases) }}</h3>
                <p class="text-xs text-slate-500 mt-1">{{ $totalSales }} Penjualan &bull; {{ $totalPurchases }} Pembelian</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>
    </div>

    <!-- Two Columns Layout for Activity and Alerts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Low Stock Alert Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-bell text-amber-500"></i>
                        <h2 class="font-bold text-slate-800 text-base">Produk Stok Kritis / Menipis</h2>
                    </div>
                    <a href="{{ route('products.index', ['low_stock' => 1]) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                        Lihat Semua &rarr;
                    </a>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-3">Produk</th>
                                <th class="px-4 py-3 text-center">Stok Saat Ini</th>
                                <th class="px-4 py-3 text-center">Batas Min</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($lowStockProducts as $prod)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-5 py-3.5">
                                        <div class="font-medium text-slate-800">{{ $prod->name }}</div>
                                        <div class="text-xs text-slate-400 font-mono">{{ $prod->sku }} &bull; {{ $prod->category->name }}</div>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                            {{ $prod->stock }} unit
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center text-xs text-slate-500 font-medium">
                                        {{ $prod->minimum_stock }} unit
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <a href="{{ route('purchases.create') }}" class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-semibold transition">
                                            <i class="fa-solid fa-truck-ramp-box"></i>
                                            <span>Restok</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-slate-400 text-sm">
                                        <i class="fa-solid fa-circle-check text-emerald-500 text-2xl mb-2 block"></i>
                                        Semua stok produk saat ini berada di atas batas minimum.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="p-3 bg-slate-50 border-t border-slate-100 text-xs text-slate-500 text-center">
                Sistem otomatis memberi peringatan jika stok &le; batas minimum stok.
            </div>
        </div>

        <!-- Recent Sales Transactions -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-clock-rotate-left text-emerald-600"></i>
                        <h2 class="font-bold text-slate-800 text-base">Penjualan Terakhir</h2>
                    </div>
                    <a href="{{ route('sales.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                        Lihat Semua &rarr;
                    </a>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-3">No. Invoice</th>
                                <th class="px-4 py-3">Pelanggan</th>
                                <th class="px-4 py-3 text-right">Total</th>
                                <th class="px-5 py-3 text-right">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentSales as $sale)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-5 py-3.5">
                                        <div class="font-mono font-semibold text-slate-800 text-xs">{{ $sale->sale_number }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $sale->sold_at->format('d M Y, H:i') }}</div>
                                    </td>
                                    <td class="px-4 py-3.5 text-xs text-slate-700 font-medium">
                                        {{ $sale->customer_name }}
                                        <div class="text-[10px] text-slate-400">Kasir: {{ $sale->user->name }}</div>
                                    </td>
                                    <td class="px-4 py-3.5 text-right font-bold text-slate-800 text-xs">
                                        Rp {{ number_format($sale->total, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <a href="{{ route('sales.show', $sale) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
                                            <i class="fa-solid fa-chevron-right text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-slate-400 text-sm">
                                        Belum ada riwayat transaksi penjualan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="p-3 bg-slate-50 border-t border-slate-100 text-xs text-slate-500 text-center">
                Total omset penjualan keseluruhan: <strong class="text-slate-700">Rp {{ number_format($salesAllTime, 0, ',', '.') }}</strong>
            </div>
        </div>

    </div>

</div>
@endsection
