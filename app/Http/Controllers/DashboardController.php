<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display live inventory & sales metrics dashboard.
     */
    public function index()
    {
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalSuppliers = Supplier::count();
        $totalStockUnits = Product::sum('stock');

        // Low stock metrics (where current stock <= minimum_stock)
        $lowStockQuery = Product::with('category')->whereColumn('stock', '<=', 'minimum_stock');
        $lowStockCount = $lowStockQuery->count();
        $lowStockProducts = $lowStockQuery->orderBy('stock', 'asc')->take(6)->get();

        // Purchases aggregate
        $totalPurchases = Purchase::where('status', 'completed')->count();
        $totalPurchaseAmount = Purchase::where('status', 'completed')->sum('total');

        // Sales aggregate
        $totalSales = Sale::where('status', 'completed')->count();
        $salesToday = Sale::where('status', 'completed')->whereDate('sold_at', today())->sum('total');
        $salesThisMonth = Sale::where('status', 'completed')
            ->whereMonth('sold_at', now()->month)
            ->whereYear('sold_at', now()->year)
            ->sum('total');
        $salesAllTime = Sale::where('status', 'completed')->sum('total');

        // Recent activity
        $recentSales = Sale::with('user')->latest('sold_at')->take(5)->get();
        $recentPurchases = Purchase::with(['supplier', 'user'])->latest('purchased_at')->take(5)->get();

        return view('dashboard.index', compact(
            'totalProducts',
            'totalCategories',
            'totalSuppliers',
            'totalStockUnits',
            'lowStockCount',
            'lowStockProducts',
            'totalPurchases',
            'totalPurchaseAmount',
            'totalSales',
            'salesToday',
            'salesThisMonth',
            'salesAllTime',
            'recentSales',
            'recentPurchases'
        ));
    }
}
