<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mellat_api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->index();
            $table->unsignedBigInteger('amount'); // TOMAN
            $table->string('ref_id')->nullable();
            $table->string('sale_order_id')->nullable();
            $table->string('sale_reference_id')->nullable();
            $table->string('res_code')->nullable();
            $table->string('status')->default('pending');
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mellat_api_logs');
    }
};
