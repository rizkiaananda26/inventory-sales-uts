<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    /**
     * Display a listing of sales with search, date, status filters and pagination.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Sale::with(['user', 'items.product']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('sale_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($startDate) {
            $query->whereDate('sold_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('sold_at', '<=', $endDate);
        }

        $sales = $query->latest('sold_at')->paginate(10)->withQueryString();

        return view('sales.index', compact('sales', 'search', 'status', 'startDate', 'endDate'));
    }

    /**
     * Show the form for creating a new sale (stock-out).
     */
    public function create()
    {
        // Only products with positive stock can be sold
        $products = Product::where('stock', '>', 0)->orderBy('name')->get();

        return view('sales.create', compact('products'));
    }

    /**
     * Store a newly created sale in storage using atomic DB transaction.
     * Enforces Slide 16 rules: lockForUpdate, server-calculated prices and totals, strict stock validation.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => ['nullable', 'string', 'max:100'],
            'sold_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ], [
            'items.required' => 'Minimal satu produk harus dipilih dalam transaksi penjualan.',
            'items.*.quantity.min' => 'Kuantitas barang yang dijual harus minimal 1 unit.',
        ]);

        $sale = DB::transaction(function () use ($request) {
            // Generate unique invoice number: INV-YYYYMMDD-XXXX
            $datePrefix = date('Ymd', strtotime($request->sold_at));
            $countToday = Sale::whereDate('sold_at', $request->sold_at)->count() + 1;
            $saleNumber = 'INV-' . $datePrefix . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            while (Sale::where('sale_number', $saleNumber)->exists()) {
                $saleNumber = 'INV-' . $datePrefix . '-' . rand(1000, 9999);
            }

            $sale = Sale::create([
                'user_id' => Auth::id(),
                'sale_number' => $saleNumber,
                'customer_name' => $request->customer_name ?: 'Pelanggan Umum',
                'total' => 0,
                'status' => 'completed',
                'notes' => $request->notes,
                'sold_at' => $request->sold_at,
            ]);

            $total = 0;

            // Prevent duplicate product entries in the same submission by aggregating quantities
            $aggregatedItems = [];
            foreach ($request->items as $itemData) {
                $pId = (int) $itemData['product_id'];
                $qty = (int) $itemData['quantity'];
                if (isset($aggregatedItems[$pId])) {
                    $aggregatedItems[$pId] += $qty;
                } else {
                    $aggregatedItems[$pId] = $qty;
                }
            }

            foreach ($aggregatedItems as $productId => $qty) {
                // Lock row for update to prevent race conditions & overselling
                $product = Product::lockForUpdate()->findOrFail($productId);

                if ($qty < 1) {
                    throw ValidationException::withMessages([
                        'items' => "Kuantitas untuk produk {$product->name} tidak valid.",
                    ]);
                }

                // Strict business rule: Penjualan tidak boleh melebihi stok yang tersedia
                if ($product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'items' => "Stok untuk produk '{$product->name}' (SKU: {$product->sku}) tidak mencukupi. Tersedia: {$product->stock} unit, diminta: {$qty} unit.",
                    ]);
                }

                // Take official selling price from database, NOT trusted from browser input
                $unitPrice = $product->selling_price;
                $subtotal = $unitPrice * $qty;

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);

                // Atomically decrement stock
                $product->decrement('stock', $qty);

                $total += $subtotal;
            }

            // Server-side calculated total update
            $sale->update(['total' => $total]);

            return $sale;
        });

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Transaksi penjualan ' . $sale->sale_number . ' berhasil diproses dan stok telah berkurang.');
    }

    /**
     * Display the specified sale details and printable receipt.
     */
    public function show(Sale $sale)
    {
        $sale->load(['user', 'items.product']);

        return view('sales.show', compact('sale'));
    }

    /**
     * Cancel sale transaction (Admin only) and restore stock.
     */
    public function cancel(Sale $sale)
    {
        if ($sale->isCancelled()) {
            return back()->with('error', 'Transaksi penjualan ini sudah dalam status dibatalkan.');
        }

        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item->product_id);
                // Restore stock back into inventory
                $product->increment('stock', $item->quantity);
            }

            $sale->update(['status' => 'cancelled']);
        });

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Transaksi penjualan berhasil dibatalkan dan stok produk telah dikembalikan ke sistem.');
    }
}
