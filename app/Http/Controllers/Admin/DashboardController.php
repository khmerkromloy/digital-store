<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
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
            'orders_pending' => Order::where('status', 'pending')->count(),
            'customers' => Customer::count(),
            'messages' => ContactMessage::where('status', 'new')->count(),
            'payments' => Payment::count(),
            'revenue' => (float) Payment::where('status', 'succeeded')->sum('amount'),
        ];

        $recentOrders = Order::with(['customer', 'branch'])
            ->orderByDesc('id')
            ->take(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }
}
