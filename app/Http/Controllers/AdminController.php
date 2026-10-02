<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $todayStart = Carbon::now('Asia/Jakarta')->startOfDay()->utc();
        $tomorrowStart = $todayStart->copy()->addDay();

        return view('admin', [
            'adminName' => Auth::user()->name,
            'productCount' => Product::query()->count(),
            'lowStockCount' => Product::query()->where('stock', '<=', 5)->count(),
            'outOfStockCount' => Product::query()->where('stock', 0)->count(),
            'heldCount' => Transaction::query()->where('status', 'held')->count(),
            'todayTransactions' => Transaction::query()
                ->where('status', 'paid')
                ->whereBetween('created_at', [$todayStart, $tomorrowStart])
                ->count(),
            'todayRevenue' => Transaction::query()
                ->where('status', 'paid')
                ->whereBetween('created_at', [$todayStart, $tomorrowStart])
                ->sum('grand_total'),
            'recentTransactions' => Transaction::query()
                ->with('user:id,name')
                ->where('status', 'paid')
                ->latest()
                ->limit(8)
                ->get(),
            'lowStockProducts' => Product::query()
                ->where('stock', '<=', 5)
                ->orderBy('stock')
                ->orderBy('name')
                ->limit(6)
                ->get(['id', 'name', 'price', 'stock']),
        ]);
    }
}
