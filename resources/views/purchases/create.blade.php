@extends('layouts.app')

@section('title', 'Catat Pembelian Baru')
@section('page-title', 'Formulir Pembelian / Restok Barang')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <div class="border-b border-slate-100 pb-4 mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Catat Pembelian (Stock-In)</h1>
                <p class="text-xs text-slate-500">Stok produk yang dicatat di sini akan otomatis bertambah ke sistem secara atomik.</p>
            </div>
            <a href="{{ route('purchases.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                &larr; Kembali
            </a>
        </div>

        <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm" class="space-y-6">
            @csrf

            <!-- Header Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100">
                <div>
                    <label for="supplier_id" class="block text-xs font-semibold text-slate-700 mb-1 uppercase tracking-wider">
                        Pilih Supplier / Pemasok <span class="text-red-500">*</span>
                    </label>
                    <select name="supplier_id" id="supplier_id" required
                        class="w-full px-3 py-2.5 rounded-xl border @error('supplier_id') border-red-400 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                                {{ $sup->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="purchased_at" class="block text-xs font-semibold text-slate-700 mb-1 uppercase tracking-wider">
                        Tanggal Transaksi <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="purchased_at" id="purchased_at" required value="{{ old('purchased_at', date('Y-m-d')) }}"
                        class="w-full px-3 py-2.5 rounded-xl border @error('purchased_at') border-red-400 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    @error('purchased_at')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="notes" class="block text-xs font-semibold text-slate-700 mb-1 uppercase tracking-wider">Catatan Tambahan (Opsional)</label>
                    <input type="text" name="notes" id="notes" value="{{ old('notes') }}" placeholder="Contoh: No faktur supplier, nomor resi pengiriman..."
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
            </div>

            <!-- Items Table Dynamic Form -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Daftar Barang yang Dibeli</h3>
                    <button type="button" id="addItemBtn" class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-semibold transition">
                        <i class="fa-solid fa-plus"></i>
                        <span>Tambah Baris Produk</span>
                    </button>
                </div>

                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-xs" id="itemsTable">
                        <thead class="bg-slate-100 text-slate-600 uppercase font-semibold border-b border-slate-200">
                            <tr>
                                <th class="p-3 w-1/2">Pilih Produk</th>
                                <th class="p-3 w-28 text-center">Jumlah (Qty)</th>
                                <th class="p-3 w-36 text-right">Harga Beli Satuan (Rp)</th>
                                <th class="p-3 w-36 text-right">Subtotal</th>
                                <th class="p-3 w-12 text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody" class="divide-y divide-slate-100">
                            <!-- Rows injected by JavaScript -->
                        </tbody>
                        <tfoot class="bg-slate-50 border-t border-slate-200 font-bold">
                            <tr>
                                <td colspan="3" class="p-3 text-right text-slate-700 uppercase">Estimasi Total Pembelian:</td>
                                <td class="p-3 text-right text-emerald-700 text-sm" id="grandTotalText">Rp 0</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <p class="text-[11px] text-slate-400 mt-2">
                    <i class="fa-solid fa-shield-halved text-emerald-600 mr-1"></i>
                    Catatan keamanan: Total transaksi final dihitung langsung oleh server menggunakan kalkulasi terproteksi.
                </p>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                <a href="{{ route('purchases.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50 text-sm font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-md shadow-emerald-600/30 transition">
                    <i class="fa-solid fa-check mr-1.5"></i> Simpan Transaksi &amp; Tambah Stok
                </button>
            </div>
        </form>
    </div>

</div>

<!-- Products JSON for dynamic row selection -->
<script>
    const availableProducts = @json($products);
    let rowIndex = 0;

    function addRow(productId = '', quantity = 1, unitCost = '') {
        const tbody = document.getElementById('itemsBody');
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-50/60 transition';
        tr.dataset.index = rowIndex;

        let optionsHtml = '<option value="">-- Pilih Produk --</option>';
        availableProducts.forEach(p => {
            const selected = (p.id == productId) ? 'selected' : '';
            optionsHtml += `<option value="${p.id}" data-cost="${p.purchase_price}" ${selected}>${p.sku} - ${p.name} (Stok Saat Ini: ${p.stock})</option>`;
        });

        tr.innerHTML = `
            <td class="p-2.5">
                <select name="items[${rowIndex}][product_id]" required class="product-select w-full px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    ${optionsHtml}
                </select>
            </td>
            <td class="p-2.5 text-center">
                <input type="number" name="items[${rowIndex}][quantity]" min="1" value="${quantity}" required
                    class="qty-input w-24 text-center px-2 py-1.5 rounded-lg border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </td>
            <td class="p-2.5 text-right">
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][unit_cost]" value="${unitCost}" required placeholder="0"
                    class="cost-input w-32 text-right px-2 py-1.5 rounded-lg border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </td>
            <td class="p-2.5 text-right font-semibold text-slate-800 row-subtotal">
                Rp 0
            </td>
            <td class="p-2.5 text-center">
                <button type="button" class="remove-row-btn text-red-500 hover:text-red-700 p-1">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);

        // Attach listeners
        const productSelect = tr.querySelector('.product-select');
        const qtyInput = tr.querySelector('.qty-input');
        const costInput = tr.querySelector('.cost-input');
        const removeBtn = tr.querySelector('.remove-row-btn');

        productSelect.addEventListener('change', () => {
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const cost = selectedOption.dataset.cost;
            if (cost && !costInput.value) {
                costInput.value = cost;
            }
            calculateRowSubtotal(tr);
        });

        qtyInput.addEventListener('input', () => calculateRowSubtotal(tr));
        costInput.addEventListener('input', () => calculateRowSubtotal(tr));

        removeBtn.addEventListener('click', () => {
            if (document.querySelectorAll('#itemsBody tr').length > 1) {
                tr.remove();
                updateGrandTotal();
            } else {
                alert('Minimal harus ada satu item dalam transaksi pembelian.');
            }
        });

        if (productId) {
            calculateRowSubtotal(tr);
        }

        rowIndex++;
    }

    function calculateRowSubtotal(tr) {
        const qty = parseFloat(tr.querySelector('.qty-input').value) || 0;
        const cost = parseFloat(tr.querySelector('.cost-input').value) || 0;
        const subtotal = qty * cost;
        tr.querySelector('.row-subtotal').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
        updateGrandTotal();
    }

    function updateGrandTotal() {
        let total = 0;
        document.querySelectorAll('#itemsBody tr').forEach(tr => {
            const qty = parseFloat(tr.querySelector('.qty-input').value) || 0;
            const cost = parseFloat(tr.querySelector('.cost-input').value) || 0;
            total += (qty * cost);
        });
        document.getElementById('grandTotalText').innerText = 'Rp ' + total.toLocaleString('id-ID');
    }

    document.getElementById('addItemBtn').addEventListener('click', () => addRow());

    // Initialize with 1 empty row on load
    document.addEventListener('DOMContentLoaded', () => {
        addRow();
    });
</script>
@endsection
