@extends('layouts.app')

@section('title', 'Catat Penjualan Baru')
@section('page-title', 'Formulir Penjualan / Kasir (Stok Keluar)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <div class="border-b border-slate-100 pb-4 mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Kasir &amp; Penjualan (Stock-Out)</h1>
                <p class="text-xs text-slate-500">Stok produk akan berkurang otomatis setelah transaksi penjualan berhasil divalidasi dan disimpan.</p>
            </div>
            <a href="{{ route('sales.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                &larr; Kembali
            </a>
        </div>

        <form action="{{ route('sales.store') }}" method="POST" id="saleForm" class="space-y-6">
            @csrf

            <!-- Header Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100">
                <div>
                    <label for="customer_name" class="block text-xs font-semibold text-slate-700 mb-1 uppercase tracking-wider">
                        Nama Pelanggan / Customer (Opsional)
                    </label>
                    <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', 'Pelanggan Umum') }}" placeholder="Pelanggan Umum / Nama Pembeli"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label for="sold_at" class="block text-xs font-semibold text-slate-700 mb-1 uppercase tracking-wider">
                        Tanggal Penjualan <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="sold_at" id="sold_at" required value="{{ old('sold_at', date('Y-m-d')) }}"
                        class="w-full px-3 py-2.5 rounded-xl border @error('sold_at') border-red-400 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    @error('sold_at')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="notes" class="block text-xs font-semibold text-slate-700 mb-1 uppercase tracking-wider">Catatan Penjualan (Opsional)</label>
                    <input type="text" name="notes" id="notes" value="{{ old('notes') }}" placeholder="Contoh: Metode pembayaran tunai / transfer, catatan permintaan..."
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
            </div>

            <!-- Items Table Dynamic Form -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Daftar Barang yang Dijual</h3>
                    <button type="button" id="addItemBtn" class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-semibold transition">
                        <i class="fa-solid fa-plus"></i>
                        <span>Tambah Baris Produk</span>
                    </button>
                </div>

                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-xs" id="itemsTable">
                        <thead class="bg-slate-100 text-slate-600 uppercase font-semibold border-b border-slate-200">
                            <tr>
                                <th class="p-3 w-1/2">Pilih Produk (Stok Tersedia)</th>
                                <th class="p-3 w-28 text-center">Jumlah Beli</th>
                                <th class="p-3 w-36 text-right">Harga Jual Satuan</th>
                                <th class="p-3 w-36 text-right">Subtotal</th>
                                <th class="p-3 w-12 text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody" class="divide-y divide-slate-100">
                            <!-- Rows injected by JavaScript -->
                        </tbody>
                        <tfoot class="bg-slate-50 border-t border-slate-200 font-bold">
                            <tr>
                                <td colspan="3" class="p-3 text-right text-slate-700 uppercase">Total Tagihan Penjualan:</td>
                                <td class="p-3 text-right text-emerald-700 text-sm" id="grandTotalText">Rp 0</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Business rules info banner (Slide 8 & 16) -->
                <div class="mt-3 p-3 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 text-xs flex items-start space-x-2">
                    <i class="fa-solid fa-circle-info text-blue-600 mt-0.5"></i>
                    <div>
                        <strong>Aturan Bisnis Integritas:</strong> Harga satuan diambil langsung dari database server, kuantitas tidak boleh melebihi stok yang tersedia, dan transaksi dibungkus dengan <code class="font-mono font-bold">DB::transaction</code> serta row locking (<code class="font-mono font-bold">lockForUpdate</code>) untuk mencegah overselling.
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                <a href="{{ route('sales.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50 text-sm font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-md shadow-emerald-600/30 transition">
                    <i class="fa-solid fa-cart-shopping mr-1.5"></i> Proses Transaksi &amp; Potong Stok
                </button>
            </div>
        </form>
    </div>

</div>

<!-- Products JSON for dynamic row selection -->
<script>
    const availableProducts = @json($products);
    let rowIndex = 0;

    function addRow(productId = '', quantity = 1) {
        const tbody = document.getElementById('itemsBody');
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-50/60 transition';
        tr.dataset.index = rowIndex;

        let optionsHtml = '<option value="">-- Pilih Produk yang Tersedia --</option>';
        availableProducts.forEach(p => {
            const selected = (p.id == productId) ? 'selected' : '';
            optionsHtml += `<option value="${p.id}" data-price="${p.selling_price}" data-stock="${p.stock}" ${selected}>${p.sku} - ${p.name} (Tersedia: ${p.stock} unit &bull; Rp ${parseInt(p.selling_price).toLocaleString('id-ID')})</option>`;
        });

        tr.innerHTML = `
            <td class="p-2.5">
                <select name="items[${rowIndex}][product_id]" required class="product-select w-full px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    ${optionsHtml}
                </select>
                <div class="stock-warning text-[11px] text-red-500 font-medium hidden mt-1"></div>
            </td>
            <td class="p-2.5 text-center">
                <input type="number" name="items[${rowIndex}][quantity]" min="1" value="${quantity}" required
                    class="qty-input w-24 text-center px-2 py-1.5 rounded-lg border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </td>
            <td class="p-2.5 text-right font-medium text-slate-700 unit-price-text">
                Rp 0
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
        const removeBtn = tr.querySelector('.remove-row-btn');

        productSelect.addEventListener('change', () => calculateRowSubtotal(tr));
        qtyInput.addEventListener('input', () => calculateRowSubtotal(tr));

        removeBtn.addEventListener('click', () => {
            if (document.querySelectorAll('#itemsBody tr').length > 1) {
                tr.remove();
                updateGrandTotal();
            } else {
                alert('Minimal harus ada satu item dalam transaksi penjualan.');
            }
        });

        if (productId) {
            calculateRowSubtotal(tr);
        }

        rowIndex++;
    }

    function calculateRowSubtotal(tr) {
        const productSelect = tr.querySelector('.product-select');
        const qtyInput = tr.querySelector('.qty-input');
        const warningDiv = tr.querySelector('.stock-warning');
        const unitPriceText = tr.querySelector('.unit-price-text');
        const subtotalText = tr.querySelector('.row-subtotal');

        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const price = parseFloat(selectedOption?.dataset?.price) || 0;
        const maxStock = parseInt(selectedOption?.dataset?.stock) || 0;
        const qty = parseInt(qtyInput.value) || 0;

        unitPriceText.innerText = 'Rp ' + price.toLocaleString('id-ID');

        if (productSelect.value) {
            qtyInput.max = maxStock;
            if (qty > maxStock) {
                warningDiv.innerText = `Melebihi stok yang ada! Maksimal ${maxStock} unit.`;
                warningDiv.classList.remove('hidden');
                qtyInput.classList.add('border-red-500', 'bg-red-50');
            } else {
                warningDiv.classList.add('hidden');
                qtyInput.classList.remove('border-red-500', 'bg-red-50');
            }
        } else {
            warningDiv.classList.add('hidden');
        }

        const subtotal = qty * price;
        subtotalText.innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
        updateGrandTotal();
    }

    function updateGrandTotal() {
        let total = 0;
        document.querySelectorAll('#itemsBody tr').forEach(tr => {
            const productSelect = tr.querySelector('.product-select');
            const qtyInput = tr.querySelector('.qty-input');
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const price = parseFloat(selectedOption?.dataset?.price) || 0;
            const qty = parseInt(qtyInput.value) || 0;
            total += (qty * price);
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
