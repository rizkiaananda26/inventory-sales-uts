@extends('layouts.app')

@section('title', 'Detail Pembelian: ' . $purchase->purchase_number)
@section('page-title', 'Detail Pembelian')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Navigation Bar -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('purchases.index') }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Riwayat Pembelian</span>
        </a>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm transition">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Bukti Pembelian</span>
            </button>
            @if(auth()->user()->isAdmin() && $purchase->isCompleted())
                <form action="{{ route('purchases.cancel', $purchase) }}" method="POST" onsubmit="return confirm('Peringatan: Membatalkan pembelian ini akan mengurangi kembali stok produk yang sebelumnya masuk. Lanjutkan pembatalan?');">
                    @csrf
                    <button type="submit" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-semibold shadow-sm transition">
                        <i class="fa-solid fa-ban"></i>
                        <span>Batalkan Pembelian</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Purchase Order Invoice Sheet -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b border-slate-100 pb-6 mb-6 gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Surat Bukti Penerimaan Barang</span>
                <h1 class="text-2xl font-black text-slate-800 font-mono mt-1">{{ $purchase->purchase_number }}</h1>
                <p class="text-xs text-slate-400 mt-0.5">Tanggal: {{ $purchase->purchased_at->translatedFormat('d F Y, H:i') }}</p>
            </div>
            <div class="text-right">
                <span class="text-xs text-slate-400 block uppercase font-semibold">Status Transaksi</span>
                @if($purchase->isCompleted())
                    <span class="inline-flex items-center space-x-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 mt-1">
                        <i class="fa-solid fa-check"></i>
                        <span>Selesai (Stok Diterima)</span>
                    </span>
                @else
                    <span class="inline-flex items-center space-x-1 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 mt-1">
                        <i class="fa-solid fa-xmark"></i>
                        <span>Dibatalkan (Stok Ditarik Kembali)</span>
                    </span>
                @endif
            </div>
        </div>

        <!-- Supplier & Staff Info Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8 text-xs">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                <span class="font-bold text-slate-500 uppercase tracking-wider block mb-2">Informasi Supplier</span>
                <div class="font-bold text-slate-800 text-sm">{{ $purchase->supplier->name }}</div>
                @if($purchase->supplier->phone)
                    <div class="text-slate-600"><i class="fa-solid fa-phone mr-1 text-slate-400"></i>{{ $purchase->supplier->phone }}</div>
                @endif
                @if($purchase->supplier->email)
                    <div class="text-slate-600"><i class="fa-solid fa-envelope mr-1 text-slate-400"></i>{{ $purchase->supplier->email }}</div>
                @endif
                @if($purchase->supplier->address)
                    <div class="text-slate-500 mt-1">{{ $purchase->supplier->address }}</div>
                @endif
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                <span class="font-bold text-slate-500 uppercase tracking-wider block mb-2">Petugas &amp; Catatan</span>
                <div class="text-slate-700"><strong class="text-slate-800">Petugas Input:</strong> {{ $purchase->user->name }} ({{ ucfirst($purchase->user->role) }})</div>
                <div class="text-slate-700"><strong class="text-slate-800">Email:</strong> {{ $purchase->user->email }}</div>
                <div class="text-slate-700 mt-2">
                    <strong class="text-slate-800">Catatan:</strong>
                    <span class="text-slate-600">{{ $purchase->notes ?: 'Tidak ada catatan tambahan.' }}</span>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="border border-slate-200 rounded-xl overflow-hidden mb-6">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 uppercase font-semibold border-b border-slate-200">
                    <tr>
                        <th class="p-3">#</th>
                        <th class="p-3">SKU &amp; Nama Produk</th>
                        <th class="p-3 text-center">Jumlah Masuk</th>
                        <th class="p-3 text-right">Harga Beli Satuan</th>
                        <th class="p-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($purchase->items as $idx => $item)
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-3 text-slate-400">{{ $idx + 1 }}</td>
                            <td class="p-3">
                                <div class="font-bold text-slate-800">{{ $item->product ? $item->product->name : 'Produk telah dihapus' }}</div>
                                <div class="font-mono text-slate-400 text-[11px]">{{ $item->product ? $item->product->sku : '-' }}</div>
                            </td>
                            <td class="p-3 text-center font-bold text-emerald-700">+{{ $item->quantity }} unit</td>
                            <td class="p-3 text-right text-slate-700">Rp {{ number_format($item->unit_cost, 0, ',', '.') }}</td>
                            <td class="p-3 text-right font-bold text-slate-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 border-t border-slate-200 font-bold">
                    <tr>
                        <td colspan="4" class="p-3.5 text-right text-slate-700 uppercase text-xs">Total Pembelian Bersih:</td>
                        <td class="p-3.5 text-right text-emerald-700 text-base font-black">Rp {{ number_format($purchase->total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="text-[11px] text-slate-400 flex items-center justify-between border-t border-slate-100 pt-4">
            <div>Terdaftar di sistem: {{ $purchase->created_at->format('d/m/Y H:i:s') }}</div>
            <div>Dokumen Resmi &bull; Inventory &amp; Sales Management System</div>
        </div>
    </div>

</div>
@endsection
