<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar campos de dirección a la tabla users
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('street')->nullable()->after('phone');   // calle + número
            $table->string('city')->nullable()->after('street');
            $table->string('commune')->nullable()->after('city');
            $table->string('zip')->nullable()->after('commune');
        });

        // Agregar campos de dirección y envío a la tabla orders
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_name')->after('customer_email');
            $table->string('shipping_email')->after('shipping_name');
            $table->string('shipping_phone')->nullable()->after('shipping_email');
            $table->string('shipping_street')->after('shipping_phone');
            $table->string('shipping_city')->after('shipping_street');
            $table->string('shipping_commune')->nullable()->after('shipping_city');
            $table->string('shipping_zip')->nullable()->after('shipping_commune');
            $table->string('shipping_type')->default('prepagado')->after('total'); // prepagado o por_pagar
            $table->decimal('shipping_cost', 10, 2)->default(0)->after('shipping_type');
        });
    }

    public function down(): void
    {
        // Quitar campos de users
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'street', 'city', 'commune', 'zip']);
        });

        // Quitar campos de orders
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_name',
                'shipping_email',
                'shipping_phone',
                'shipping_street',
                'shipping_city',
                'shipping_commune',
                'shipping_zip',
                'shipping_type',
                'shipping_cost'
            ]);
        });
    }
};
