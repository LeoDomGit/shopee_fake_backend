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
       if (Schema::hasTable('brands')) {
        Schema::table('brands', function (Blueprint $table) {
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
