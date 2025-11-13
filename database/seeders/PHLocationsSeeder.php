<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Province;
use App\Models\Municipality;
use App\Models\Barangay;

class PHLocationsSeeder extends Seeder
{
    public function run(): void
    {
        //path on JSON file
        $path = resource_path('js/data/ph-locations.json');

        if (!File::exists($path)) {
            $this->command->error('ph-locations.json not found at ' . $path);
            return;
        }

        $json = File::get($path);
        $data = json_decode($json, true);

        foreach ($data as $provinceName => $provinceData) {
            // Create Province
            $province = Province::firstOrCreate(['name' => $provinceName]);

            // Loop municipalities
            foreach ($provinceData['municipality_list'] as $municipalityName => $municipalityData) {
                $municipality = Municipality::firstOrCreate([
                    'name' => $municipalityName,
                    'province_id' => $province->id
                ]);

                // Loop barangays
                foreach ($municipalityData['barangay_list'] as $barangayName) {
                    Barangay::firstOrCreate([
                        'name' => $barangayName,
                        'municipality_id' => $municipality->id
                    ]);
                }
            }
        }

        $this->command->info('PH locations seeded successfully');
    }
}
