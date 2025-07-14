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
    Schema::create('orders', function (Blueprint $table) {
        $table->id();

        // Relación con usuarios (opcional si compra como invitado)
        $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

        // Datos del cliente (si es invitado)
        $table->string('customer_name');
        $table->string('customer_email');

        // Total de la compra
        $table->decimal('total', 10, 2);

        // Opcionales para Flow / control interno
        $table->string('status')->default('pendiente');      // pendiente, pagado, fallido, cancelado, etc.
        $table->string('flow_order_id')->nullable();          // ID que Flow devuelve al crear la orden
        $table->text('flow_response')->nullable();            // JSON crudo con los datos de Flow (para respaldo)

        $table->timestamps();
    });
    }


    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
