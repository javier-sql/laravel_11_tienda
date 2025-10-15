<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Renombrar commune → commune_id
            if (Schema::hasColumn('users', 'commune')) {
                $table->renameColumn('commune', 'commune_id');
            }

            $table->unsignedBigInteger('commune_id')->nullable()->change();

            // Agregar columnas nuevas si no existen
            if (!Schema::hasColumn('users', 'number')) {
                $table->string('number')->nullable()->after('street');
            }

            if (!Schema::hasColumn('users', 'unit')) {
                $table->string('unit')->nullable()->after('number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revertir los cambios
            if (Schema::hasColumn('users', 'commune_id')) {
                $table->renameColumn('commune_id', 'commune');
            }

            if (Schema::hasColumn('users', 'number')) {
                $table->dropColumn('number');
            }

            if (Schema::hasColumn('users', 'unit')) {
                $table->dropColumn('unit');
            }
        });
    }
};
