<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the customer dashboard with products for ticket submission.
     */
    public function __invoke(Request $request): View
    {
        $products = Product::orderBy('name')->get();

        return view('dashboards.customer', [
            'products' => $products,
        ]);
    }
}
