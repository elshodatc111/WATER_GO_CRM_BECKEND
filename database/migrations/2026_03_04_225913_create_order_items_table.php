<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    public function up(): void{
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->integer('quantity'); // Nechta (masalan: 2 ta 18L suv)
            $table->decimal('price', 10, 2); // Sotilgan vaqtdagi bitta dona mahsulot narxi
            $table->decimal('total', 10, 2); // quantity * price (jami summa)
            $table->timestamps();
        });
    }

    public function down(): void{
        Schema::dropIfExists('order_items');
    }
};
