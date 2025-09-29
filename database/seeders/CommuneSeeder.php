<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommuneSeeder extends Seeder
{
    public function run(): void
    {
        $communes = [
            ['name' => 'Macul', 'price' => 3000],
            ['name' => 'Ñuñoa', 'price' => 3000],
            ['name' => 'Providencia', 'price' => 4000],
            ['name' => 'San Joaquin', 'price' => 2000],
            ['name' => 'San Miguel', 'price' => 1000],
            ['name' => 'Santiago Centro', 'price' => 3000],
            ['name' => 'La Reina', 'price' => 4500],
            ['name' => 'Las Condes', 'price' => 5500],
            ['name' => 'Lo Barnechea', 'price' => 7000],
            ['name' => 'Peñalolén', 'price' => 4000],
            ['name' => 'Vitacura', 'price' => 6000],
            ['name' => 'Colina', 'price' => 12000],
            ['name' => 'Conchalí', 'price' => 5000],
            ['name' => 'Huechuraba', 'price' => 4500],
            ['name' => 'Independencia', 'price' => 3500],
            ['name' => 'Lampa', 'price' => 12000],
            ['name' => 'Quilicura', 'price' => 6000],
            ['name' => 'Recoleta', 'price' => 3500],
            ['name' => 'Renca', 'price' => 5000],
            ['name' => 'Tiltil', 'price' => 16500],
            ['name' => 'Calera de Tango', 'price' => 6500],
            ['name' => 'Cerrillos', 'price' => 4000],
            ['name' => 'Cerro Navia', 'price' => 5000],
            ['name' => 'Estación Central', 'price' => 3500],
            ['name' => 'Lo Espejo', 'price' => 1500],
            ['name' => 'Lo Prado', 'price' => 4000],
            ['name' => 'Maipú', 'price' => 5000],
            ['name' => 'Padre Hurtado', 'price' => 7000],
            ['name' => 'Pedro Aguirre Cerda', 'price' => 2000],
            ['name' => 'Pudahuel', 'price' => 5500],
            ['name' => 'Quinta Normal', 'price' => 4000],
            ['name' => 'Buin', 'price' => 8500],
            ['name' => 'El Bosque', 'price' => 2500],
            ['name' => 'La Cisterna', 'price' => 2000],
            ['name' => 'La Florida', 'price' => 3000],
            ['name' => 'La Granja', 'price' => 2000],
            ['name' => 'La Pintana', 'price' => 4000],
            ['name' => 'Paine', 'price' => 12000],
            ['name' => 'Pirque', 'price' => 8500],
            ['name' => 'Puente Alto', 'price' => 5000],
            ['name' => 'San Bernardo', 'price' => 4000],
            ['name' => 'San José de Maipo', 'price' => 17000],
            ['name' => 'San Ramón', 'price' => 2000],
        ];

        DB::table('communes')->insert($communes);
    }
}
