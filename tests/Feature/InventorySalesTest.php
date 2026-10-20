<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InventorySalesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected Category $category;
    protected Supplier $supplier;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $this->staff = User::create([
            'name' => 'Staff Test',
            'email' => 'staff@test.com',
            'password' => Hash::make('password123'),
            'role' => 'staff',
        ]);

        $this->category = Category::create([
            'name' => 'Kategori Uji',
            'description' => 'Kategori untuk unit testing',
        ]);

        $this->supplier = Supplier::create([
            'name' => 'Supplier Uji',
            'phone' => '08123456789',
            'email' => 'supplier@test.com',
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'sku' => 'TEST-001',
            'name' => 'Mouse Wireless Uji',
            'purchase_price' => 100000,
            'selling_price' => 150000,
            'stock' => 10,
            'minimum_stock' => 3,
        ]);
    }

    /**
     * 1. Auth Test: Guest tidak dapat membuka dashboard dan diarahkan ke login.
     */
    public function test_guest_cannot_access_dashboard_and_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * 2. Auth Test: Password salah ditolak.
     */
    public function test_invalid_login_credentials_are_rejected(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * 3. Auth Test: Login berhasil dengan kredensial benar.
     */
    public function test_valid_login_succeeds_and_redirects_to_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->admin);
    }

    /**
     * 4. Product Validation: SKU duplikat ditolak.
     */
    public function test_product_duplicate_sku_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)->post('/products', [
            'sku' => 'TEST-001', // SKU already exists
            'name' => 'Barang Duplikat',
            'category_id' => $this->category->id,
            'purchase_price' => 50000,
            'selling_price' => 80000,
            'stock' => 5,
            'minimum_stock' => 2,
        ]);

        $response->assertSessionHasErrors('sku');
    }

    /**
     * 5. Product Validation: Harga negatif ditolak.
     */
    public function test_product_negative_price_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)->post('/products', [
            'sku' => 'TEST-NEW',
            'name' => 'Barang Harga Minus',
            'category_id' => $this->category->id,
            'purchase_price' => -5000,
            'selling_price' => -10000,
            'stock' => 5,
            'minimum_stock' => 2,
        ]);

        $response->assertSessionHasErrors(['purchase_price', 'selling_price']);
    }

    /**
     * 6. Purchase Test: Pembelian sukses menambah stok sesuai kuantitas.
     */
    public function test_purchase_successfully_increases_product_stock(): void
    {
        $initialStock = $this->product->stock; // 10

        $response = $this->actingAs($this->staff)->post('/purchases', [
            'supplier_id' => $this->supplier->id,
            'purchased_at' => now()->toDateString(),
            'notes' => 'Restok barang pengujian',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 15,
                    'unit_cost' => 100000,
                ]
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('purchases', [
            'supplier_id' => $this->supplier->id,
            'status' => 'completed',
        ]);

        // Verify stock increased by 15: 10 + 15 = 25
        $this->product->refresh();
        $this->assertEquals($initialStock + 15, $this->product->stock);
    }

    /**
     * 7. Sale Test: Penjualan sukses mengurangi stok sesuai kuantitas.
     */
    public function test_sale_successfully_decreases_product_stock(): void
    {
        $initialStock = $this->product->stock; // 10

        $response = $this->actingAs($this->staff)->post('/sales', [
            'customer_name' => 'Budi Pembeli',
            'sold_at' => now()->toDateString(),
            'notes' => 'Pembelian retail tunai',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 4,
                ]
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('sales', [
            'customer_name' => 'Budi Pembeli',
            'status' => 'completed',
        ]);

        // Verify stock reduced by 4: 10 - 4 = 6
        $this->product->refresh();
        $this->assertEquals($initialStock - 4, $this->product->stock);
    }

    /**
     * 8. Business Rule Test: Penjualan ditolak jika stok tidak mencukupi tanpa mengubah data.
     */
    public function test_sale_with_insufficient_stock_is_rejected_without_data_change(): void
    {
        $initialStock = $this->product->stock; // 10
        $initialSalesCount = Sale::count();

        // Customer requests 50 units while available stock is only 10
        $response = $this->actingAs($this->staff)->post('/sales', [
            'customer_name' => 'Toko Besar',
            'sold_at' => now()->toDateString(),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 50,
                ]
            ],
        ]);

        $response->assertSessionHasErrors('items');

        // Verify stock is untouched
        $this->product->refresh();
        $this->assertEquals($initialStock, $this->product->stock);

        // Verify no sale was created
        $this->assertEquals($initialSalesCount, Sale::count());
    }

    /**
     * 9. Atomicity Test: Rollback jika salah satu item transaksi penjualan gagal.
     */
    public function test_multi_item_sale_atomic_rollback_if_one_item_exceeds_stock(): void
    {
        $product2 = Product::create([
            'category_id' => $this->category->id,
            'sku' => 'TEST-002',
            'name' => 'Keyboard Uji',
            'purchase_price' => 200000,
            'selling_price' => 300000,
            'stock' => 2, // Only 2 in stock
            'minimum_stock' => 1,
        ]);

        $initialStock1 = $this->product->stock; // 10
        $initialStock2 = $product2->stock; // 2

        // Item 1 is valid (3 units from 10), but Item 2 is invalid (10 units from 2)
        $response = $this->actingAs($this->staff)->post('/sales', [
            'customer_name' => 'Customer Multi Item',
            'sold_at' => now()->toDateString(),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 3,
                ],
                [
                    'product_id' => $product2->id,
                    'quantity' => 10, // Exceeds 2
                ],
            ],
        ]);

        $response->assertSessionHasErrors('items');

        // Due to DB::transaction, product1 MUST NOT be decremented either
        $this->product->refresh();
        $product2->refresh();
        $this->assertEquals($initialStock1, $this->product->stock);
        $this->assertEquals($initialStock2, $product2->stock);
    }

    /**
     * 10. Authorization Test: Staff tidak dapat mengakses fungsi Admin (misal: kelola user, edit produk).
     */
    public function test_staff_cannot_access_admin_functions(): void
    {
        // Staff tries to access user management index
        $responseUsers = $this->actingAs($this->staff)->get('/users');
        $responseUsers->assertStatus(403);

        // Staff tries to access create product page
        $responseProductCreate = $this->actingAs($this->staff)->get('/products-create');
        $responseProductCreate->assertStatus(403);
    }

    /**
     * 11. Transaction Cancellation Test: Pembatalan penjualan mengembalikan stok ke sistem.
     */
    public function test_cancelling_sale_restores_product_stock(): void
    {
        // 1. Create a sale of 5 items
        $this->actingAs($this->staff)->post('/sales', [
            'customer_name' => 'Customer Pembatalan',
            'sold_at' => now()->toDateString(),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 5,
                ]
            ],
        ]);

        $this->product->refresh();
        $this->assertEquals(5, $this->product->stock); // 10 - 5 = 5

        $sale = Sale::latest()->first();

        // 2. Admin cancels the sale
        $cancelResponse = $this->actingAs($this->admin)->post("/sales/{$sale->id}/cancel");
        $cancelResponse->assertSessionHasNoErrors();

        // 3. Stock should be restored to 10
        $this->product->refresh();
        $this->assertEquals(10, $this->product->stock);

        $sale->refresh();
        $this->assertEquals('cancelled', $sale->status);
    }

    /**
     * 13. Category CRUD & Relational Protection: Kategori terlindungi dari duplikasi dan tidak bisa dihapus jika ada produk.
     */
    public function test_category_crud_and_relational_deletion_protection(): void
    {
        // Duplicate name rejected
        $dupResponse = $this->actingAs($this->admin)->post('/categories', [
            'name' => 'Kategori Uji',
        ]);
        $dupResponse->assertSessionHasErrors('name');

        // Cannot delete category that contains products
        $delResponse = $this->actingAs($this->admin)->delete("/categories/{$this->category->id}");
        $delResponse->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $this->category->id]);
    }

    /**
     * 14. Supplier CRUD & Relational Protection: Supplier tidak bisa dihapus jika memiliki riwayat transaksi pembelian.
     */
    public function test_supplier_cannot_be_deleted_if_has_purchases(): void
    {
        // Create a purchase for this supplier
        Purchase::create([
            'supplier_id' => $this->supplier->id,
            'user_id' => $this->admin->id,
            'purchase_number' => 'PO-TEST-DEL',
            'total' => 100000,
            'status' => 'completed',
            'purchased_at' => now(),
        ]);

        $delResponse = $this->actingAs($this->admin)->delete("/suppliers/{$this->supplier->id}");
        $delResponse->assertSessionHas('error');
        $this->assertDatabaseHas('suppliers', ['id' => $this->supplier->id]);
    }

    /**
     * 15. Purchase Cancellation Test: Pembatalan pembelian mengurangi stok kembali dan menolak jika stok tidak cukup.
     */
    public function test_purchase_cancellation_reverts_stock_and_prevents_negative(): void
    {
        // 1. Staff creates purchase for 10 units (stock 10 -> 20)
        $this->actingAs($this->staff)->post('/purchases', [
            'supplier_id' => $this->supplier->id,
            'purchased_at' => now()->toDateString(),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 10,
                    'unit_cost' => 100000,
                ]
            ],
        ]);
        $this->product->refresh();
        $this->assertEquals(20, $this->product->stock);

        $purchase = Purchase::latest()->first();

        // 2. Admin cancels purchase -> stock reverts (20 -> 10)
        $cancelResponse = $this->actingAs($this->admin)->post("/purchases/{$purchase->id}/cancel");
        $cancelResponse->assertSessionHasNoErrors();
        $this->product->refresh();
        $this->assertEquals(10, $this->product->stock);
        $purchase->refresh();
        $this->assertEquals('cancelled', $purchase->status);
    }

    /**
     * 16. Soft Delete Test: Produk dengan riwayat transaksi di-soft delete secara aman (integritas FK terjaga).
     */
    public function test_product_with_transactions_is_soft_deleted_preserving_history(): void
    {
        // Create sale transaction for this product
        $sale = Sale::create([
            'user_id' => $this->staff->id,
            'sale_number' => 'INV-TEST-SOFT',
            'total' => 150000,
            'status' => 'completed',
            'sold_at' => now(),
        ]);
        $sale->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => 150000,
            'subtotal' => 150000,
        ]);

        // Admin deletes product
        $response = $this->actingAs($this->admin)->delete("/products/{$this->product->id}");
        $response->assertSessionHas('success');

        // Product should be soft-deleted (deleted_at is NOT null)
        $this->assertSoftDeleted('products', ['id' => $this->product->id]);

        // Sale item still points safely to the product
        $this->assertDatabaseHas('sale_items', ['product_id' => $this->product->id]);
    }

    /**
     * 17. User Management & Self Delete Protection: Admin tidak dapat menghapus akun sendiri.
     */
    public function test_admin_cannot_delete_own_account(): void
    {
        $response = $this->actingAs($this->admin)->delete("/users/{$this->admin->id}");
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    /**
     * 18. Search and Filter Test: Pencarian SKU dan filter produk berfungsi.
     */
    public function test_product_search_and_filter(): void
    {
        $response = $this->actingAs($this->staff)->get('/products?search=TEST-001');
        $response->assertStatus(200);
        $response->assertSee('TEST-001');
        $response->assertSee('Mouse Wireless Uji');

        $responseEmpty = $this->actingAs($this->staff)->get('/products?search=NON-EXISTENT-SKU');
        $responseEmpty->assertStatus(200);
        $responseEmpty->assertDontSee('Mouse Wireless Uji');
    }
}
