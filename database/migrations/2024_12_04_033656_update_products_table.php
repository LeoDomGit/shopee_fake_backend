<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('brand_id')->after('discount');
                $table->unsignedBigInteger('category_id')->after('brand_id');
                $table->unsignedBigInteger('shop_id')->after('category_id');
                $table->foreign('brand_id')->references('id')->on('brands');
                $table->foreign('category_id')->references('id')->on('categories');
                $table->foreign('shop_id')->references('id')->on('shops');

            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
