<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AddressService
{
    /**
     * Valida que la dirección (calle y número) pertenezca a la comuna indicada
     *
     * @param string $communeName
     * @param string $street
     * @param string $number
     * @return bool
     */
    public function validateCommuneAddress(string $communeName, string $street, string $number): bool
    {
        sleep(2); // Evitar saturar la API

        $address = trim("$number $street, Chile");

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'MiAplicacion/1.0 (contacto@midominio.cl)'
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $address,
                'format' => 'json',
                'addressdetails' => 1,
                'limit' => 1
            ]);

            $data = $response->json();

            if (!empty($data) && isset($data[0]['address'])) {
                $addressDetails = $data[0]['address'];
                $osmSuburb = $addressDetails['suburb'] ?? $addressDetails['city_district'] ?? $addressDetails['city'] ?? null;

                Log::info('Comuna detectada por API', [
                    'input_commune' => $communeName,
                    'osm_suburb' => $osmSuburb
                ]);

                if ($osmSuburb) {
                    return mb_strtolower($osmSuburb) === mb_strtolower($communeName);
                }
            }

        } catch (\Exception $e) {
            Log::error('Error validando dirección con Nominatim', [
                'address' => $address,
                'error' => $e->getMessage()
            ]);
        }

        return false;
    }
}
