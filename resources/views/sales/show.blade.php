@extends('layouts.app')

@section('title', 'Struk Penjualan: ' . $sale->sale_number)
@section('page-title', 'Struk & Faktur Penjualan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('sales.index') }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Riwayat Penjualan</span>
        </a>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm transition">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Struk / Invoice</span>
            </button>
            @if(auth()->user()->isAdmin() && $sale->isCompleted())
                <form action="{{ route('sales.cancel', $sale) }}" method="POST" onsubmit="return confirm('Peringatan: Membatalkan penjualan ini akan mengembalikan stok produk ke sistem secara otomatis. Lanjutkan pembatalan?');">
                    @csrf
                    <button type="submit" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-semibold shadow-sm transition">
                        <i class="fa-solid fa-ban"></i>
                        <span>Batalkan Penjualan</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Official Sales Receipt / Invoice Sheet -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <!-- Receipt Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b border-slate-100 pb-6 mb-6 gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Faktur Penjualan Resmi</span>
                <h1 class="text-2xl font-black text-slate-800 font-mono mt-1">{{ $sale->sale_number }}</h1>
                <p class="text-xs text-slate-400 mt-0.5">Waktu Transaksi: {{ $sale->sold_at->translatedFormat('d F Y, H:i') }}</p>
            </div>
            <div class="text-right">
                <span class="text-xs text-slate-400 block uppercase font-semibold">Status Transaksi</span>
                @if($sale->isCompleted())
                    <span class="inline-flex items-center space-x-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 mt-1">
                        <i class="fa-solid fa-check"></i>
                        <span>Lunas &amp; Selesai</span>
                    </span>
                @else
                    <span class="inline-flex items-center space-x-1 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 mt-1">
                        <i class="fa-solid fa-xmark"></i>
                        <span>Dibatalkan (Stok Dipulihkan)</span>
                    </span>
                @endif
            </div>
        </div>

        <!-- Customer & Staff Info Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8 text-xs">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                <span class="font-bold text-slate-500 uppercase tracking-wider block mb-2">Informasi Pembeli / Customer</span>
                <div class="font-bold text-slate-800 text-sm">{{ $sale->customer_name }}</div>
                <div class="text-slate-500">Tipe: Retail / Umum</div>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                <span class="font-bold text-slate-500 uppercase tracking-wider block mb-2">Kasir &amp; Catatan</span>
                <div class="text-slate-700"><strong class="text-slate-800">Kasir / Operator:</strong> {{ $sale->user->name }}</div>
                <div class="text-slate-700 mt-1">
                    <strong class="text-slate-800">Catatan:</strong>
                    <span class="text-slate-600">{{ $sale->notes ?: 'Terima kasih atas kunjungan dan transaksi Anda.' }}</span>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="border border-slate-200 rounded-xl overflow-hidden mb-6">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 uppercase font-semibold border-b border-slate-200">
                    <tr>
                        <th class="p-3">#</th>
                        <th class="p-3">Item Produk</th>
                        <th class="p-3 text-center">Jumlah Beli</th>
                        <th class="p-3 text-right">Harga Satuan</th>
                        <th class="p-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($sale->items as $idx => $item)
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-3 text-slate-400">{{ $idx + 1 }}</td>
                            <td class="p-3">
                                <div class="font-bold text-slate-800">{{ $item->product ? $item->product->name : 'Produk telah diarsipkan' }}</div>
                                <div class="font-mono text-slate-400 text-[11px]">{{ $item->product ? $item->product->sku : '-' }}</div>
                            </td>
                            <td class="p-3 text-center font-bold text-slate-800">{{ $item->quantity }} unit</td>
                            <td class="p-3 text-right text-slate-700">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="p-3 text-right font-bold text-slate-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 border-t border-slate-200 font-bold">
                    <tr>
                        <td colspan="4" class="p-3.5 text-right text-slate-700 uppercase text-xs">Total Pembayaran:</td>
                        <td class="p-3.5 text-right text-emerald-700 text-base font-black">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="text-[11px] text-slate-400 flex items-center justify-between border-t border-slate-100 pt-4">
            <div>Struk ini sah dicetak dari sistem: {{ now()->translatedFormat('d F Y, H:i:s') }}</div>
            <div>Inventory &amp; Sales Management System</div>
        </div>
    </div>

</div>
@endsection
