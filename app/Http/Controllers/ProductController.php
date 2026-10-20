<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of products with search, category filter, low stock filter, and pagination.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $lowStock = $request->boolean('low_stock');

        $query = Product::with('category');

        if ($search) {
            $query->search($search);
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($lowStock) {
            $query->lowStock();
        }

        $products = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'search', 'categoryId', 'lowStock'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
        ], [
            'sku.required' => 'SKU produk wajib diisi.',
            'sku.unique' => 'SKU produk sudah terdaftar. SKU harus unik.',
            'name.required' => 'Nama produk wajib diisi.',
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'purchase_price.required' => 'Harga beli wajib diisi.',
            'purchase_price.numeric' => 'Harga beli harus berupa angka.',
            'purchase_price.min' => 'Harga beli tidak boleh bernilai negatif.',
            'selling_price.required' => 'Harga jual wajib diisi.',
            'selling_price.numeric' => 'Harga jual harus berupa angka.',
            'selling_price.min' => 'Harga jual tidak boleh bernilai negatif.',
            'stock.required' => 'Stok awal wajib diisi.',
            'stock.integer' => 'Stok harus berupa bilangan bulat.',
            'stock.min' => 'Stok tidak boleh bernilai negatif.',
            'minimum_stock.required' => 'Batas minimum stok wajib diisi.',
            'minimum_stock.integer' => 'Batas minimum stok harus berupa bilangan bulat.',
            'minimum_stock.min' => 'Batas minimum stok tidak boleh negatif.',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produk baru dengan SKU ' . $validated['sku'] . ' berhasil ditambahkan.');
    }

    /**
     * Display the specified product details and transaction history.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'purchaseItems.purchase.supplier', 'saleItems.sale.user']);

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($product->id)],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
        ], [
            'sku.required' => 'SKU produk wajib diisi.',
            'sku.unique' => 'SKU sudah digunakan oleh produk lain.',
            'name.required' => 'Nama produk wajib diisi.',
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'purchase_price.min' => 'Harga beli tidak boleh negatif.',
            'selling_price.min' => 'Harga jual tidak boleh negatif.',
            'stock.min' => 'Stok tidak boleh negatif.',
            'minimum_stock.min' => 'Minimum stok tidak boleh negatif.',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Data produk ' . $product->name . ' berhasil diperbarui.');
    }

    /**
     * Remove the specified product from storage (soft delete / safe integrity check).
     */
    public function destroy(Product $product)
    {
        // Slide 8 rule: Produk yang sudah dipakai dalam transaksi tidak dihapus secara hard delete
        $hasPurchases = $product->purchaseItems()->count() > 0;
        $hasSales = $product->saleItems()->count() > 0;

        if ($hasPurchases || $hasSales) {
            // Soft delete preserving foreign keys in historical records
            $product->delete();
            return redirect()->route('products.index')
                ->with('success', 'Produk memiliki riwayat transaksi, sehingga diarsipkan secara aman (soft delete) untuk menjaga integritas data laporan.');
        }

        $product->forceDelete();
        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus secara permanen.');
    }
}
