<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{

    public function up(): void{
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('direktor');
            $table->string('phone',18)->unique();
            $table->string('address');
            $table->decimal('balance', 12, 2)->default(0);
            $table->decimal('service_fee', 10, 2)->default(0);
            $table->decimal('rating', 3, 2)->default(0.00)->index();
            $table->unsignedInteger('rating_count')->default(0);
            $table->string('inn', 14)->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->string('working_hours')->nullable();
            $table->decimal('latitude', 10, 7)->nullable()->index();
            $table->decimal('longitude', 10, 7)->nullable()->index();
            $table->unsignedInteger('delivery_radius')->nullable();
            $table->timestamps();
        });
    }
    
    public function down(): void{
        Schema::dropIfExists('companies');
    }

};
