<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{

    public function up(): void{
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('total_count')->default(0); // Jami mahsulotlar soni
            $table->decimal('total_price', 10, 2); // Mahsulotlar summasi
            $table->decimal('delivery_price', 10, 2)->default(0); // Yetkazib berish narxi (alohida bo'lgani yaxshi)
            $table->decimal('final_price', 10, 2); // Jami: total_price + delivery_price
            // Holatlar (Statuslar)
            $table->enum('status', ['pending', 'qabul_qilindi', 'canceled', 'yetkazilmoqda', 'yetkazildi'])->default('pending');
            $table->enum('payment_method', ['cash', 'card'])->default('cash');
            $table->enum('payment_status', ['pending', 'success', 'refunded'])->default('pending');
            // Manzil va Geolokatsiya
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable()->index();
            $table->decimal('longitude', 10, 7)->nullable()->index();
            // Kuryer (Courier) bilan bog'liq qismlar
            $table->foreignId('courier_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('courier_comment')->nullable();
            $table->enum('courier_rating', ['1', '2', '3', '4', '5'])->nullable();
            $table->string('courier_rating_text')->nullable();
            // Vaqtlar
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }
    
    public function down(): void{
        Schema::dropIfExists('orders');
    }

};
