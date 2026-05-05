<?php

use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderItemController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductKeyController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/* ------------------------------------------------------------------
 | Public storefront (Phase 1)
 -----------------------------------------------------------------*/
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/data', [ProductController::class, 'data'])->name('data');
    Route::get('/{product:slug}', [ProductController::class, 'show'])->name('show');
});

Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/{category:slug}', [CategoryController::class, 'show'])->name('show');
});

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');

Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

/* ------------------------------------------------------------------
 | Locale switcher (POST sets cookie; React component handles display)
 -----------------------------------------------------------------*/
Route::post('/locale/{locale}', [LocaleController::class, 'set'])
    ->name('locale.set')
    ->whereIn('locale', ['en', 'km']);

/* ------------------------------------------------------------------
 | Auth (admin login only — customers come in phase 2C)
 -----------------------------------------------------------------*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

/* ------------------------------------------------------------------
 | Admin backend
 -----------------------------------------------------------------*/
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    /* Branches */
    Route::get('branches/data', [BranchController::class, 'data'])->name('branches.data');
    Route::resource('branches', BranchController::class);

    /* Categories */
    Route::get('categories/data', [AdminCategoryController::class, 'data'])->name('categories.data');
    Route::resource('categories', AdminCategoryController::class);

    /* Products */
    Route::get('products/data', [AdminProductController::class, 'data'])->name('products.data');
    Route::resource('products', AdminProductController::class);

    /* Product keys */
    Route::get('product-keys/data', [ProductKeyController::class, 'data'])->name('product-keys.data');
    Route::resource('product-keys', ProductKeyController::class);

    /* Branch-scoped inventory */
    Route::get('inventory/data', [InventoryController::class, 'data'])->name('inventory.data');
    Route::resource('inventory', InventoryController::class);

    /* Customers */
    Route::get('customers/data', [CustomerController::class, 'data'])->name('customers.data');
    Route::resource('customers', CustomerController::class);

    /* Orders + nested order item sub-resource */
    Route::get('orders/data', [OrderController::class, 'data'])->name('orders.data');
    Route::post('orders/{order}/recalculate', [OrderController::class, 'recalculate'])->name('orders.recalculate');
    Route::post('orders/{order}/mark-paid', [OrderController::class, 'markPaid'])->name('orders.mark-paid');
    Route::resource('orders', OrderController::class);
    Route::get('orders/{order}/items/create', [OrderItemController::class, 'create'])->name('orders.items.create');
    Route::post('orders/{order}/items', [OrderItemController::class, 'store'])->name('orders.items.store');
    Route::delete('orders/{order}/items/{item}', [OrderItemController::class, 'destroy'])->name('orders.items.destroy');

    /* Payments */
    Route::get('payments/data', [PaymentController::class, 'data'])->name('payments.data');
    Route::resource('payments', PaymentController::class);
});
