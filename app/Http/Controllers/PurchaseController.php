<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    /**
     * Display a listing of purchases with search, supplier, status, date filters and pagination.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $supplierId = $request->input('supplier_id');
        $status = $request->input('status');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Purchase::with(['supplier', 'user', 'items.product']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('purchase_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($startDate) {
            $query->whereDate('purchased_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('purchased_at', '<=', $endDate);
        }

        $purchases = $query->latest('purchased_at')->paginate(10)->withQueryString();
        $suppliers = Supplier::orderBy('name')->get();

        return view('purchases.index', compact('purchases', 'suppliers', 'search', 'supplierId', 'status', 'startDate', 'endDate'));
    }

    /**
     * Show the form for creating a new purchase (stock-in).
     */
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    /**
     * Store a newly created purchase in storage using atomic DB transaction.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchased_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ], [
            'supplier_id.required' => 'Pemasok wajib dipilih.',
            'supplier_id.exists' => 'Pemasok tidak valid.',
            'items.required' => 'Minimal satu produk harus ditambahkan dalam pembelian.',
            'items.*.quantity.min' => 'Kuantitas barang harus minimal 1.',
            'items.*.unit_cost.min' => 'Harga beli barang tidak boleh negatif.',
        ]);

        $purchase = DB::transaction(function () use ($request) {
            // Generate unique purchase order number: PO-YYYYMMDD-XXXX
            $datePrefix = date('Ymd', strtotime($request->purchased_at));
            $countToday = Purchase::whereDate('purchased_at', $request->purchased_at)->count() + 1;
            $purchaseNumber = 'PO-' . $datePrefix . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            // If collision happens due to concurrent calls, add unique random suffix
            while (Purchase::where('purchase_number', $purchaseNumber)->exists()) {
                $purchaseNumber = 'PO-' . $datePrefix . '-' . rand(1000, 9999);
            }

            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'user_id' => Auth::id(),
                'purchase_number' => $purchaseNumber,
                'total' => 0,
                'status' => 'completed',
                'notes' => $request->notes,
                'purchased_at' => $request->purchased_at,
            ]);

            $total = 0;

            foreach ($request->items as $itemData) {
                $qty = (int) $itemData['quantity'];
                $unitCost = (float) $itemData['unit_cost'];

                if ($qty < 1) {
                    throw ValidationException::withMessages([
                        'items' => 'Kuantitas produk tidak boleh kurang dari 1.',
                    ]);
                }

                // Lock product to ensure atomicity
                $product = Product::lockForUpdate()->findOrFail($itemData['product_id']);

                $subtotal = $unitCost * $qty;

                $purchase->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_cost' => $unitCost,
                    'subtotal' => $subtotal,
                ]);

                // Update product stock (stock-in)
                $product->increment('stock', $qty);

                // Optionally update purchase_price with newest purchase cost
                $product->update(['purchase_price' => $unitCost]);

                $total += $subtotal;
            }

            // Server-side calculated total
            $purchase->update(['total' => $total]);

            return $purchase;
        });

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Transaksi pembelian ' . $purchase->purchase_number . ' berhasil disimpan dan stok bertambah.');
    }

    /**
     * Display the specified purchase details and items.
     */
    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'user', 'items.product']);

        return view('purchases.show', compact('purchase'));
    }

    /**
     * Cancel purchase transaction (Admin only) and revert stock.
     */
    public function cancel(Purchase $purchase)
    {
        if ($purchase->isCancelled()) {
            return back()->with('error', 'Transaksi pembelian ini sudah dalam status dibatalkan.');
        }

        DB::transaction(function () use ($purchase) {
            foreach ($purchase->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item->product_id);

                // Check if current stock is sufficient to deduct back
                if ($product->stock < $item->quantity) {
                    throw ValidationException::withMessages([
                        'error' => "Gagal membatalkan pembelian: Stok produk '{$product->name}' tersisa {$product->stock}, tidak mencukupi untuk dikurangi {$item->quantity} unit karena kemungkinan sudah terjual.",
                    ]);
                }

                $product->decrement('stock', $item->quantity);
            }

            $purchase->update(['status' => 'cancelled']);
        });

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Transaksi pembelian berhasil dibatalkan dan stok produk telah dipulihkan (dikurangi kembali).');
    }
}
