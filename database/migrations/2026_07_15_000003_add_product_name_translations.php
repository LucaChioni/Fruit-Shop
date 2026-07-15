<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('products', 'name_en')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('name_en')->nullable()->after('name');
            });
        }

        if (! Schema::hasColumn('order_items', 'product_name_en')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->string('product_name_en')->nullable()->after('product_name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('order_items', 'product_name_en')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('product_name_en');
            });
        }

        if (Schema::hasColumn('products', 'name_en')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('name_en');
            });
        }
    }
};
