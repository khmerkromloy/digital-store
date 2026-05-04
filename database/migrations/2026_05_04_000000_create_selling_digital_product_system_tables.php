<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Selling Digital Product System - Laravel 12
     *
     * This single migration creates:
     * - Users, customers, admins, roles, permissions
     * - Categories, products, product versions, attributes
     * - Digital files, file access rules
     * - Inventory for license keys, download codes, vouchers
     * - Cart, orders, payments, refunds, webhooks
     * - Download links, download logs, license assignments
     * - Email templates/logs, coupons
     * - Media gallery, SEO metadata
     * - Settings, social links, security logs, audit logs
     * - Report exports and Laravel queue tables
     */
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Auth, Roles, Permissions
        |--------------------------------------------------------------------------
        */

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable()->index();
            $table->string('password');
            $table->string('status', 30)->default('active')->index(); // active, blocked, pending
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // super_admin, admin, manager, customer
            $table->string('guard_name')->default('web');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // product.create, order.view
            $table->string('group')->nullable()->index(); // product, order, payment
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();

            $table->primary(['user_id', 'role_id']);
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();

            $table->primary(['role_id', 'permission_id']);
        });

        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('customer_code')->unique();
            $table->string('company_name')->nullable();
            $table->string('billing_name')->nullable();
            $table->string('billing_email')->nullable();
            $table->string('billing_phone')->nullable();
            $table->text('billing_address')->nullable();
            $table->string('country', 100)->nullable();
            $table->decimal('total_spent', 14, 2)->default(0);
            $table->unsignedInteger('total_orders')->default(0);
            $table->timestamps();
        });

        Schema::create('admin_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('admin_code')->unique();
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->boolean('can_login_admin')->default(true);
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | Media first, then avatar FK to avoid circular dependency
        |--------------------------------------------------------------------------
        */

        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('disk')->default('public'); // local, public, s3
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type')->nullable()->index();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('alt_text')->nullable();
            $table->string('title')->nullable();
            $table->json('metadata')->nullable(); // width, height, duration, etc.
            $table->timestamps();
            $table->softDeletes();

            $table->index(['disk', 'path']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('avatar_media_id')
                ->nullable()
                ->after('password')
                ->constrained('media')
                ->nullOnDelete();
        });

        /*
        |--------------------------------------------------------------------------
        | Category and Product Management
        |--------------------------------------------------------------------------
        */

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('image_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->integer('sort_order')->default(0);
            $table->string('status', 30)->default('active')->index(); // active, inactive
            $table->timestamps();
            $table->softDeletes();

            $table->index(['parent_id', 'sort_order']);
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->nullable()->unique();
            $table->string('product_type', 50)->index(); // software, ebook, course, template, plugin, theme, license, media
            $table->string('fulfillment_type', 50)->index(); // download, license_key, voucher, manual, mixed
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->decimal('price', 14, 2)->default(0);
            $table->decimal('compare_price', 14, 2)->nullable();
            $table->string('currency', 10)->default('USD');
            $table->foreignId('thumbnail_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('status', 30)->default('draft')->index(); // draft, active, inactive, archived
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
            $table->index(['product_type', 'fulfillment_type']);
        });

        Schema::create('product_category', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();

            $table->primary(['product_id', 'category_id']);
        });

        Schema::create('product_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('version');
            $table->longText('changelog')->nullable();
            $table->date('release_date')->nullable();
            $table->boolean('is_latest')->default(false)->index();
            $table->string('status', 30)->default('active')->index(); // active, deprecated
            $table->timestamps();

            $table->unique(['product_id', 'version']);
        });

        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('name');
            $table->text('value')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'sort_order']);
        });

        /*
        |--------------------------------------------------------------------------
        | Digital Files
        |--------------------------------------------------------------------------
        */

        Schema::create('digital_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('product_version_id')->nullable()->constrained('product_versions')->nullOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('original_name');
            $table->string('disk')->default('private'); // local, s3, private
            $table->string('path');
            $table->string('mime_type')->nullable();
            $table->string('extension', 20)->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('checksum')->nullable();
            $table->unsignedInteger('download_limit')->nullable();
            $table->unsignedInteger('expires_after_days')->nullable();
            $table->boolean('is_primary')->default(false)->index();
            $table->string('status', 30)->default('active')->index(); // active, inactive, archived
            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_id', 'status']);
            $table->index(['disk', 'path']);
        });

        Schema::create('file_access_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('digital_file_id')->constrained('digital_files')->cascadeOnDelete();
            $table->string('rule_type', 50); // paid_only, role_based, product_owned
            $table->json('value')->nullable();
            $table->string('status', 30)->default('active')->index();
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | Inventory: License Keys, Download Codes, Vouchers
        |--------------------------------------------------------------------------
        */

        Schema::create('inventory_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('batch_no')->unique();
            $table->string('inventory_type', 50)->index(); // license_key, download_code, voucher
            $table->string('name');
            $table->unsignedInteger('quantity_total')->default(0);
            $table->unsignedInteger('quantity_available')->default(0);
            $table->unsignedInteger('quantity_sold')->default(0);
            $table->timestamp('expires_at')->nullable()->index();
            $table->string('status', 30)->default('active')->index(); // active, inactive, exhausted
            $table->timestamps();

            $table->index(['product_id', 'inventory_type', 'status']);
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_batch_id')->constrained('inventory_batches')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->text('code_encrypted'); // encrypted license/voucher/download code
            $table->string('code_hash', 128)->unique(); // duplicate check without exposing code
            $table->string('status', 30)->default('available')->index(); // available, reserved, sold, revoked, expired
            $table->timestamp('reserved_until')->nullable()->index();
            $table->timestamp('sold_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'status']);
            $table->index(['inventory_batch_id', 'status']);
        });

        /*
        |--------------------------------------------------------------------------
        | Cart and Orders
        |--------------------------------------------------------------------------
        */

        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('session_id')->nullable()->index();
            $table->string('currency', 10)->default('USD');
            $table->string('status', 30)->default('active')->index(); // active, converted, abandoned
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('carts')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('total_price', 14, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['cart_id', 'product_id']);
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_email')->index();
            $table->string('customer_name')->nullable();
            $table->string('currency', 10)->default('USD');
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount_total', 14, 2)->default(0);
            $table->decimal('tax_total', 14, 2)->default(0);
            $table->decimal('fee_total', 14, 2)->default(0);
            $table->decimal('grand_total', 14, 2)->default(0);
            $table->string('order_status', 30)->default('pending')->index(); // pending, processing, completed, cancelled, refunded
            $table->string('payment_status', 30)->default('unpaid')->index(); // unpaid, paid, failed, refunded, partial_refund
            $table->string('fulfillment_status', 30)->default('pending')->index(); // pending, delivered, partial, failed
            $table->json('billing_snapshot')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable()->index();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'created_at']);
            $table->index(['order_status', 'payment_status']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('product_version_id')->nullable()->constrained('product_versions')->nullOnDelete();
            $table->string('product_name');
            $table->string('sku')->nullable();
            $table->string('fulfillment_type', 50)->index(); // download, license_key, voucher, mixed
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('total_price', 14, 2)->default(0);
            $table->timestamp('delivered_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'product_id']);
        });

        /*
        |--------------------------------------------------------------------------
        | Payments, Refunds, Webhooks
        |--------------------------------------------------------------------------
        */

        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // aba, stripe, paypal, bakong
            $table->string('name');
            $table->longText('config_encrypted')->nullable(); // encrypted JSON
            $table->boolean('is_sandbox')->default(true);
            $table->string('status', 30)->default('active')->index(); // active, inactive
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('payment_gateway_id')->nullable()->constrained('payment_gateways')->nullOnDelete();
            $table->string('transaction_no')->unique();
            $table->string('provider_transaction_id')->nullable()->index();
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->string('status', 30)->default('pending')->index(); // pending, authorized, paid, failed, cancelled, refunded
            $table->timestamp('paid_at')->nullable()->index();
            $table->text('failed_reason')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('payment_transaction_id')->constrained('payment_transactions')->cascadeOnDelete();
            $table->string('refund_no')->unique();
            $table->decimal('amount', 14, 2)->default(0);
            $table->text('reason')->nullable();
            $table->string('status', 30)->default('pending')->index(); // pending, approved, rejected, refunded, failed
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
        });

        Schema::create('payment_webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_gateway_id')->nullable()->constrained('payment_gateways')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('provider_event_id')->nullable()->index();
            $table->string('event_type')->index(); // payment.success, payment.failed
            $table->json('payload');
            $table->string('status', 30)->default('received')->index(); // received, processed, failed
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | Digital Delivery
        |--------------------------------------------------------------------------
        */

        Schema::create('download_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->foreignId('digital_file_id')->constrained('digital_files')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('token', 128)->unique();
            $table->unsignedInteger('max_downloads')->nullable();
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('revoked_at')->nullable();
            $table->string('status', 30)->default('active')->index(); // active, expired, revoked
            $table->timestamps();

            $table->index(['order_item_id', 'status']);
            $table->index(['digital_file_id', 'status']);
        });

        Schema::create('download_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('download_link_id')->constrained('download_links')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('downloaded_at')->useCurrent();
            $table->timestamps();

            $table->index(['download_link_id', 'downloaded_at']);
            $table->index(['user_id', 'downloaded_at']);
        });

        Schema::create('license_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->unique()->constrained('inventory_items')->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 30)->default('active')->index(); // active, revoked, expired
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['order_item_id', 'status']);
            $table->index(['user_id', 'status']);
        });

        /*
        |--------------------------------------------------------------------------
        | Email Templates and Logs
        |--------------------------------------------------------------------------
        */

        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // order_paid, download_ready
            $table->string('subject');
            $table->longText('body_html');
            $table->longText('body_text')->nullable();
            $table->json('variables')->nullable();
            $table->string('status', 30)->default('active')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('email_template_id')->nullable()->constrained('email_templates')->nullOnDelete();
            $table->string('to_email')->index();
            $table->string('subject');
            $table->string('status', 30)->default('queued')->index(); // queued, sent, failed
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable()->index();
            $table->timestamps();

            $table->index(['order_id', 'status']);
        });

        /*
        |--------------------------------------------------------------------------
        | Coupons and Promotions
        |--------------------------------------------------------------------------
        */

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('discount_type', 30)->index(); // fixed, percentage
            $table->decimal('discount_value', 14, 2)->default(0);
            $table->decimal('min_order_amount', 14, 2)->nullable();
            $table->decimal('max_discount_amount', 14, 2)->nullable();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('usage_per_user')->nullable();
            $table->timestamp('starts_at')->nullable()->index();
            $table->timestamp('ends_at')->nullable()->index();
            $table->string('status', 30)->default('active')->index(); // active, inactive, expired
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('coupon_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained('coupons')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->timestamp('redeemed_at')->useCurrent();
            $table->timestamps();

            $table->unique(['coupon_id', 'order_id']);
            $table->index(['user_id', 'redeemed_at']);
        });

        /*
        |--------------------------------------------------------------------------
        | Gallery, Polymorphic Media, SEO
        |--------------------------------------------------------------------------
        */

        Schema::create('mediables', function (Blueprint $table) {
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->string('mediable_type');
            $table->unsignedBigInteger('mediable_id');
            $table->string('collection')->default('default'); // gallery, thumbnail, banner
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->primary(['media_id', 'mediable_type', 'mediable_id', 'collection']);
            $table->index(['mediable_type', 'mediable_id']);
            $table->index(['collection', 'sort_order']);
        });

        Schema::create('seo_metadata', function (Blueprint $table) {
            $table->id();
            $table->string('seoable_type');
            $table->unsignedBigInteger('seoable_id');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->foreignId('og_image_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('canonical_url')->nullable();
            $table->json('schema_json')->nullable();
            $table->timestamps();

            $table->unique(['seoable_type', 'seoable_id']);
        });

        /*
        |--------------------------------------------------------------------------
        | System Settings, Social, Security, Audit
        |--------------------------------------------------------------------------
        */

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->index(); // email, payment, security, seo, social
            $table->string('key');
            $table->longText('value')->nullable();
            $table->string('value_type', 30)->default('string'); // string, boolean, number, json, encrypted
            $table->boolean('is_encrypted')->default(false);
            $table->boolean('is_public')->default(false)->index();
            $table->timestamps();

            $table->unique(['group', 'key']);
        });

        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->string('platform')->index(); // facebook, youtube, telegram
            $table->string('label');
            $table->string('url');
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('status', 30)->default('active')->index();
            $table->timestamps();
        });

        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event')->index(); // login_success, login_failed, password_changed
            $table->string('ip_address', 45)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at')->useCurrent()->index();
            $table->timestamps();

            $table->index(['user_id', 'event']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action')->index(); // created, updated, deleted, login
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('occurred_at')->useCurrent()->index();
            $table->timestamps();

            $table->index(['actor_id', 'occurred_at']);
            $table->index(['auditable_type', 'auditable_id']);
        });

        /*
        |--------------------------------------------------------------------------
        | Report Export
        |--------------------------------------------------------------------------
        */

        Schema::create('report_exports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->string('report_type')->index(); // sales, products, customers, payments
            $table->json('filters')->nullable();
            $table->string('format', 20)->default('xlsx'); // csv, xlsx, pdf
            $table->string('disk')->nullable();
            $table->string('file_path')->nullable();
            $table->string('status', 30)->default('pending')->index(); // pending, processing, completed, failed
            $table->text('error_message')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['requested_by', 'status']);
        });

        /*
        |--------------------------------------------------------------------------
        | Laravel Queue Tables
        |--------------------------------------------------------------------------
        */

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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*
         * Important:
         * Drop tables in reverse dependency order.
         */

        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');

        Schema::dropIfExists('report_exports');

        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('security_logs');
        Schema::dropIfExists('social_links');
        Schema::dropIfExists('settings');

        Schema::dropIfExists('seo_metadata');
        Schema::dropIfExists('mediables');

        Schema::dropIfExists('coupon_redemptions');
        Schema::dropIfExists('coupons');

        Schema::dropIfExists('email_logs');
        Schema::dropIfExists('email_templates');

        Schema::dropIfExists('license_assignments');
        Schema::dropIfExists('download_logs');
        Schema::dropIfExists('download_links');

        Schema::dropIfExists('payment_webhooks');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('payment_gateways');

        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');

        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('inventory_batches');

        Schema::dropIfExists('file_access_rules');
        Schema::dropIfExists('digital_files');

        Schema::dropIfExists('product_attributes');
        Schema::dropIfExists('product_versions');
        Schema::dropIfExists('product_category');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'avatar_media_id')) {
                $table->dropConstrainedForeignId('avatar_media_id');
            }
        });

        Schema::dropIfExists('media');

        Schema::dropIfExists('admin_profiles');
        Schema::dropIfExists('customer_profiles');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('users');
    }
};
