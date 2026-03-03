<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{

    public function up(): void{
        Schema::create('company_balance_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete()->index();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['naqt','card','return'])->default('naqt');
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_joriy', 15, 2);
            $table->decimal('balance_kiyingi', 15, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
    
    public function down(): void{
        Schema::dropIfExists('company_balance_transactions');
    }
};
