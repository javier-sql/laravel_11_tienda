<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('weight', 8, 2)->nullable()->after('image'); // kg
            $table->decimal('length', 8, 2)->nullable()->after('weight'); // cm
            $table->decimal('width', 8, 2)->nullable()->after('length');  // cm
            $table->decimal('height', 8, 2)->nullable()->after('width');  // cm
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['weight', 'length', 'width', 'height']);
        });
    }
};
