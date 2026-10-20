@extends('layouts.app')

@section('title', 'Detail Produk: ' . $product->name)
@section('page-title', 'Detail Produk')

@section('content')
<div class="space-y-6">

    <!-- Top Navigation & Action -->
    <div class="flex items-center justify-between">
        <a href="{{ route('products.index') }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar Produk</span>
        </a>
        <div class="space-x-2">
            @if(auth()->user()->isAdmin())
                <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center space-x-1 px-4 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold transition">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>Edit Produk</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Product Overview Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-6 mb-6">
            <div>
                <div class="flex items-center space-x-2 mb-1">
                    <span class="font-mono text-xs font-bold px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $product->sku }}
                    </span>
                    <span class="text-xs px-2.5 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-semibold border border-emerald-200">
                        {{ $product->category->name }}
                    </span>
                </div>
                <h1 class="text-2xl font-black text-slate-800">{{ $product->name }}</h1>
            </div>
            <div class="text-right">
                <span class="text-xs text-slate-400 block uppercase font-semibold">Status Stok</span>
                @if($product->isLowStock())
                    <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 mt-1">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Stok Rendah ({{ $product->stock }} unit)</span>
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 mt-1">
                        Tersedia: {{ $product->stock }} unit
                    </span>
                @endif
            </div>
        </div>

        <!-- Metric Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                <div class="text-xs text-slate-400 font-semibold uppercase">Harga Beli Terakhir</div>
                <div class="text-lg font-black text-slate-800 mt-1">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</div>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                <div class="text-xs text-slate-400 font-semibold uppercase">Harga Jual Satuan</div>
                <div class="text-lg font-black text-emerald-600 mt-1">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</div>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                <div class="text-xs text-slate-400 font-semibold uppercase">Margin Laba Satuan</div>
                @php
                    $margin = $product->selling_price - $product->purchase_price;
                    $marginPct = $product->purchase_price > 0 ? ($margin / $product->purchase_price) * 100 : 0;
                @endphp
                <div class="text-lg font-black text-indigo-600 mt-1">
                    Rp {{ number_format($margin, 0, ',', '.') }}
                    <span class="text-xs font-medium text-slate-500">({{ number_format($marginPct, 1) }}%)</span>
                </div>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                <div class="text-xs text-slate-400 font-semibold uppercase">Batas Minimum Stok</div>
                <div class="text-lg font-black text-slate-800 mt-1">{{ $product->minimum_stock }} unit</div>
            </div>
        </div>
    </div>

    <!-- Transaction History (Inbound Purchases & Outbound Sales) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Inbound Purchases -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
            <h2 class="font-bold text-slate-800 text-sm mb-3 flex items-center space-x-2">
                <i class="fa-solid fa-truck-ramp-box text-emerald-600"></i>
                <span>Riwayat Pembelian (Stok Masuk)</span>
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase border-b border-slate-100">
                        <tr>
                            <th class="py-2.5 px-3">No. PO</th>
                            <th class="py-2.5 px-3">Supplier</th>
                            <th class="py-2.5 px-3 text-center">Qty Masuk</th>
                            <th class="py-2.5 px-3 text-right">Harga Beli</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($product->purchaseItems as $pItem)
                            <tr>
                                <td class="py-2.5 px-3 font-mono font-semibold text-slate-700">
                                    <a href="{{ route('purchases.show', $pItem->purchase) }}" class="hover:text-emerald-600">
                                        {{ $pItem->purchase->purchase_number }}
                                    </a>
                                </td>
                                <td class="py-2.5 px-3 text-slate-600">{{ $pItem->purchase->supplier->name }}</td>
                                <td class="py-2.5 px-3 text-center font-bold text-emerald-700">+{{ $pItem->quantity }}</td>
                                <td class="py-2.5 px-3 text-right text-slate-600">Rp {{ number_format($pItem->unit_cost, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-slate-400">Belum ada riwayat pembelian untuk produk ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Outbound Sales -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
            <h2 class="font-bold text-slate-800 text-sm mb-3 flex items-center space-x-2">
                <i class="fa-solid fa-cash-register text-blue-600"></i>
                <span>Riwayat Penjualan (Stok Keluar)</span>
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase border-b border-slate-100">
                        <tr>
                            <th class="py-2.5 px-3">No. Invoice</th>
                            <th class="py-2.5 px-3">Pelanggan</th>
                            <th class="py-2.5 px-3 text-center">Qty Keluar</th>
                            <th class="py-2.5 px-3 text-right">Harga Jual</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($product->saleItems as $sItem)
                            <tr>
                                <td class="py-2.5 px-3 font-mono font-semibold text-slate-700">
                                    <a href="{{ route('sales.show', $sItem->sale) }}" class="hover:text-blue-600">
                                        {{ $sItem->sale->sale_number }}
                                    </a>
                                </td>
                                <td class="py-2.5 px-3 text-slate-600">{{ $sItem->sale->customer_name }}</td>
                                <td class="py-2.5 px-3 text-center font-bold text-red-600">-{{ $sItem->quantity }}</td>
                                <td class="py-2.5 px-3 text-right text-slate-600">Rp {{ number_format($sItem->unit_price, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-slate-400">Belum ada riwayat penjualan untuk produk ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
