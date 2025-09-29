<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('price');
            $table->timestamps();
        });

        // Agregamos la relación en orders
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('commune_id')->nullable()->constrained('communes');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('commune_id');
        });

        Schema::dropIfExists('communes');
    }
};
