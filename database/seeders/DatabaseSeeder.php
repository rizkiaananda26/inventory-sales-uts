<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with demo data.
     */
    public function run(): void
    {
        // 1. Users (Admin and Staff accounts)
        $admin = User::firstOrCreate(
            ['email' => 'admin@inventory.test'],
            [
                'name' => 'Administrator / Owner',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $staff = User::firstOrCreate(
            ['email' => 'staff@inventory.test'],
            [
                'name' => 'Staff Operasional',
                'password' => Hash::make('password'),
                'role' => 'staff',
            ]
        );

        // 2. Categories
        $catElectronics = Category::firstOrCreate(['name' => 'Elektronik & Gadget'], ['description' => 'Perangkat elektronik, gadget dan perlengkapan display']);
        $catAccessories = Category::firstOrCreate(['name' => 'Aksesoris Komputer'], ['description' => 'Keyboard, mouse, kabel, dan periferal PC']);
        $catStationery = Category::firstOrCreate(['name' => 'Alat Tulis Kantor'], ['description' => 'Kertas, pena, spidol, dan perlengkapan dokumen']);
        $catNetworking = Category::firstOrCreate(['name' => 'Perangkat Jaringan'], ['description' => 'Router, switch, access point, dan kabel LAN']);

        // 3. Suppliers
        $sup1 = Supplier::firstOrCreate(
            ['name' => 'PT Sinar Mandiri Distribusi'],
            [
                'phone' => '081234567890',
                'email' => 'kontak@sinarmandiri.co.id',
                'address' => 'Jl. Industri Raya No. 45, Jakarta Barat',
            ]
        );

        $sup2 = Supplier::firstOrCreate(
            ['name' => 'CV Tech Nusa Global'],
            [
                'phone' => '081987654321',
                'email' => 'sales@technusa.com',
                'address' => 'Komp. Ruko Niaga Blok B2, Bandung',
            ]
        );

        $sup3 = Supplier::firstOrCreate(
            ['name' => 'Distributor Nusantara Logistik'],
            [
                'phone' => '082112233445',
                'email' => 'support@distronusantara.id',
                'address' => 'Jl. Raya Darmo No. 88, Surabaya',
            ]
        );

        // 4. Products
        $p1 = Product::firstOrCreate(
            ['sku' => 'PRD-001'],
            [
                'category_id' => $catAccessories->id,
                'name' => 'Logitech Wireless Mouse M185',
                'purchase_price' => 110000,
                'selling_price' => 165000,
                'stock' => 25,
                'minimum_stock' => 5,
            ]
        );

        $p2 = Product::firstOrCreate(
            ['sku' => 'PRD-002'],
            [
                'category_id' => $catAccessories->id,
                'name' => 'Mechanical Keyboard RGB TKL Blue Switch',
                'purchase_price' => 320000,
                'selling_price' => 475000,
                'stock' => 12,
                'minimum_stock' => 3,
            ]
        );

        $p3 = Product::firstOrCreate(
            ['sku' => 'PRD-003'],
            [
                'category_id' => $catNetworking->id,
                'name' => 'Router WiFi Dual Band AC1200',
                'purchase_price' => 250000,
                'selling_price' => 375000,
                'stock' => 4, // Low stock alert triggered
                'minimum_stock' => 5,
            ]
        );

        $p4 = Product::firstOrCreate(
            ['sku' => 'PRD-004'],
            [
                'category_id' => $catNetworking->id,
                'name' => 'Kabel UTP Cat6 305m Roll',
                'purchase_price' => 650000,
                'selling_price' => 920000,
                'stock' => 2, // Low stock alert triggered
                'minimum_stock' => 3,
            ]
        );

        $p5 = Product::firstOrCreate(
            ['sku' => 'PRD-005'],
            [
                'category_id' => $catStationery->id,
                'name' => 'Kertas HVS A4 80gr PaperOne 1 Rim',
                'purchase_price' => 45000,
                'selling_price' => 62000,
                'stock' => 50,
                'minimum_stock' => 10,
            ]
        );

        $p6 = Product::firstOrCreate(
            ['sku' => 'PRD-006'],
            [
                'category_id' => $catStationery->id,
                'name' => 'Spidol Whiteboard Snowman Hitam (Box/12)',
                'purchase_price' => 60000,
                'selling_price' => 85000,
                'stock' => 15,
                'minimum_stock' => 5,
            ]
        );

        $p7 = Product::firstOrCreate(
            ['sku' => 'PRD-007'],
            [
                'category_id' => $catElectronics->id,
                'name' => 'Monitor LED 24 Inch IPS 100Hz Frameless',
                'purchase_price' => 1250000,
                'selling_price' => 1750000,
                'stock' => 8,
                'minimum_stock' => 2,
            ]
        );

        $p8 = Product::firstOrCreate(
            ['sku' => 'PRD-008'],
            [
                'category_id' => $catElectronics->id,
                'name' => 'Webcam Full HD 1080p with Built-in Mic',
                'purchase_price' => 180000,
                'selling_price' => 265000,
                'stock' => 1, // Low stock alert triggered
                'minimum_stock' => 4,
            ]
        );

        // 5. Initial Purchase Transaction (Stock In Demo)
        $purchaseNumber = 'PO-' . date('Ymd') . '-0001';
        $purchase = Purchase::firstOrCreate(
            ['purchase_number' => $purchaseNumber],
            [
                'supplier_id' => $sup1->id,
                'user_id' => $staff->id,
                'total' => 2350000,
                'status' => 'completed',
                'notes' => 'Pembelian stok awal operasional toko',
                'purchased_at' => now()->subDays(2),
            ]
        );

        if ($purchase->items()->count() === 0) {
            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id' => $p1->id,
                'quantity' => 10,
                'unit_cost' => 110000,
                'subtotal' => 1100000,
            ]);

            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id' => $p7->id,
                'quantity' => 1,
                'unit_cost' => 1250000,
                'subtotal' => 1250000,
            ]);
        }

        // 6. Initial Sales Transaction (Stock Out Demo)
        $saleNumber = 'INV-' . date('Ymd') . '-0001';
        $sale = Sale::firstOrCreate(
            ['sale_number' => $saleNumber],
            [
                'user_id' => $staff->id,
                'customer_name' => 'PT Solusi Teknologi Bersama',
                'total' => 805000,
                'status' => 'completed',
                'notes' => 'Penjualan retail perkantoran',
                'sold_at' => now()->subDay(),
            ]
        );

        if ($sale->items()->count() === 0) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $p1->id,
                'quantity' => 2,
                'unit_price' => 165000,
                'subtotal' => 330000,
            ]);

            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $p2->id,
                'quantity' => 1,
                'unit_price' => 475000,
                'subtotal' => 475000,
            ]);
        }
    }
}
