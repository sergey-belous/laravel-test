<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $products = Product::with(['category', 'user', 'images'])
            ->latest()
            ->paginate(12);

        return view('dashboard', compact('products'));
    }
}

