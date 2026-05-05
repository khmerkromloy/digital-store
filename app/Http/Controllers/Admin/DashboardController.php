<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductKey;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $stats = [
            'branches' => Branch::count(),
            'categories' => Category::count(),
            'products' => Product::count(),
            'keys' => ProductKey::count(),
            'keys_available' => ProductKey::where('status', 'available')->count(),
            'orders' => Order::count(),
            'customers' => Customer::count(),
            'messages' => ContactMessage::where('status', 'new')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
