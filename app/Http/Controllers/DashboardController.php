<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): RedirectResponse|View
    {
        return match ($request->user()->role) {
            'admin' => to_route('admin.index'),
            'cashier' => to_route('kasir.index'),
            default => view('customer.dashboard'),
        };
    }
}
