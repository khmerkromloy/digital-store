<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Consolidated schema for the Digital Product Selling System (multi-branch).
 *
 * One migration to rule them all. Sections (in dependency order):
 *  1. Auth: users, password_reset_tokens, sessions, cache, jobs
 *  2. RBAC: roles, permissions, role_user, permission_role
 *  3. Branches (multi-store)
 *  4. Catalog: categories, products, product_keys, branch_product (inventory)
 *  5. Customers + auth-side users for storefront
 *  6. Sales: orders, order_items, payments
 *  7. Marketing / settings: contact_messages, settings
 *  8. Audit: audit_logs
 */
return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Auth & framework tables
        |--------------------------------------------------------------------------
        */

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone')->nullable();
            $table->string('password');
            // 'admin' | 'staff' | 'customer' — staff is scoped to a branch.
            $table->string('user_type')->default('staff');
            $table->foreignId('branch_id')->nullable();
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active');
            $table->string('avatar')->nullable();
            $table->string('locale', 5)->default('en');
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        /*
        |--------------------------------------------------------------------------
        | 2. RBAC (roles & permissions)
        |--------------------------------------------------------------------------
        */

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('module');
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'role_id']);
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['permission_id', 'role_id']);
        });

        /*
        |--------------------------------------------------------------------------
        | 3. Branches (multi-store)
        |--------------------------------------------------------------------------
        */

        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('name_kh')->nullable();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Cambodia');
            $table->string('timezone')->default('Asia/Phnom_Penh');
            $table->string('currency', 8)->default('USD');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // Now that branches exists, add the FK from users.branch_id.
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
        });

        /*
        |--------------------------------------------------------------------------
        | 4. Catalog: categories, products, license keys, per-branch inventory
        |--------------------------------------------------------------------------
        */

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_kh')->nullable();
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('cover_image')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('name_kh')->nullable();
            $table->string('slug')->unique();
            $table->string('sku')->nullable()->unique();
            $table->text('short_description')->nullable();
            $table->text('short_description_kh')->nullable();
            $table->longText('description')->nullable();
            $table->longText('description_kh')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('original_price', 12, 2)->nullable();
            $table->string('currency', 8)->default('USD');
            // 'license_key' | 'account' | 'subscription' | 'gift_card' | 'other'
            $table->string('product_type')->default('license_key');
            $table->string('cover_image')->nullable();
            $table->json('images')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('sales_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('auto_deliver')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->text('key_value');
            $table->text('extra_info')->nullable();
            // 'available' | 'reserved' | 'sold' | 'expired' | 'invalid'
            $table->enum('status', ['available', 'reserved', 'sold', 'expired', 'invalid'])->default('available');
            $table->foreignId('order_id')->nullable();
            $table->foreignId('order_item_id')->nullable();
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('sold_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Per-branch inventory: how many keys/units of a product live in a branch.
        Schema::create('branch_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedInteger('stock')->default(0);
            $table->decimal('price_override', 12, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['branch_id', 'product_id']);
        });

        /*
        |--------------------------------------------------------------------------
        | 5. Customers (storefront-side users; admin staff are in `users`)
        |--------------------------------------------------------------------------
        */

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable()->index();
            $table->string('telegram_handle')->nullable();
            $table->string('country')->default('Cambodia');
            $table->string('locale', 5)->default('en');
            $table->decimal('total_spent', 14, 2)->default(0);
            $table->unsignedInteger('orders_count')->default(0);
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        /*
        |--------------------------------------------------------------------------
        | 6. Sales: orders, order_items, payments
        |--------------------------------------------------------------------------
        */

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('staff who created the order');
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount_total', 14, 2)->default(0);
            $table->decimal('tax_total', 14, 2)->default(0);
            $table->decimal('grand_total', 14, 2)->default(0);
            $table->string('currency', 8)->default('USD');
            // pending | paid | partial | refunded | cancelled | delivered | failed
            $table->enum('status', ['pending', 'paid', 'partial', 'refunded', 'cancelled', 'delivered', 'failed'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'refunded'])->default('unpaid');
            $table->enum('delivery_status', ['pending', 'delivered', 'failed'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('delivery_method')->default('email');
            $table->text('customer_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamp('placed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('product_name');
            $table->string('product_sku')->nullable();
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('line_total', 14, 2)->default(0);
            $table->json('keys_delivered')->nullable();
            $table->timestamps();
        });

        // Add the deferred FKs from product_keys → orders / order_items.
        Schema::table('product_keys', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->nullOnDelete();
            $table->foreign('order_item_id')->references('id')->on('order_items')->nullOnDelete();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('payment_number')->unique();
            $table->string('method'); // cash | bakong | aba | wing | telegram | usdt | manual
            $table->decimal('amount', 14, 2);
            $table->string('currency', 8)->default('USD');
            $table->enum('status', ['pending', 'succeeded', 'failed', 'refunded'])->default('pending');
            $table->string('reference_no')->nullable();
            $table->string('proof_image')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        /*
        |--------------------------------------------------------------------------
        | 7. Marketing & settings
        |--------------------------------------------------------------------------
        */

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('subject')->nullable();
            $table->text('message');
            $table->ipAddress('ip_address')->nullable();
            $table->string('locale', 5)->default('en');
            $table->enum('status', ['new', 'read', 'replied', 'spam'])->default('new');
            $table->foreignId('replied_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->cascadeOnDelete();
            $table->string('group')->default('general'); // general|payment|delivery|email|seo|...
            $table->string('key');
            $table->longText('value')->nullable();
            $table->string('type')->default('string'); // string|int|bool|json|text|image
            $table->timestamps();
            $table->unique(['branch_id', 'group', 'key']);
        });

        /*
        |--------------------------------------------------------------------------
        | 8. Audit logs
        |--------------------------------------------------------------------------
        */

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('action'); // created|updated|deleted|login|logout|...
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('changes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->index(['subject_type', 'subject_id']);
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        // Drop in reverse order, dropping FK-bearing tables first.
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('payments');

        // Detach deferred FKs from product_keys before dropping orders.
        Schema::table('product_keys', function (Blueprint $table) {
            $table->dropForeign(['order_item_id']);
            $table->dropForeign(['order_id']);
        });
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');

        Schema::dropIfExists('customers');
        Schema::dropIfExists('branch_product');
        Schema::dropIfExists('product_keys');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
        });
        Schema::dropIfExists('branches');

        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');

        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
