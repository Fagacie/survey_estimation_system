<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CostRate;

class CostRateSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            // MBES
            ['survey_type' => 'MBES', 'category' => 'Logistics', 'name' => 'Mod and Demod', 'unit_type' => 'Lump Sum', 'default_rate' => 30000],
            ['survey_type' => 'MBES', 'category' => 'Equipment', 'name' => 'Multibeam Rental Packages', 'unit_type' => 'Lump Sum', 'default_rate' => 70000],
            ['survey_type' => 'MBES', 'category' => 'Equipment', 'name' => 'Boat Rental', 'unit_type' => 'Per Day', 'default_rate' => 3000],
            ['survey_type' => 'MBES', 'category' => 'Equipment', 'name' => 'Tide Gauge Rental', 'unit_type' => 'Per Day', 'default_rate' => 200],
            ['survey_type' => 'MBES', 'category' => 'Logistics', 'name' => 'Leveling Work', 'unit_type' => 'Lump Sum', 'default_rate' => 5000],
            ['survey_type' => 'MBES', 'category' => 'Miscellaneous', 'name' => 'Insurance', 'unit_type' => 'Lump Sum', 'default_rate' => 5000],
            ['survey_type' => 'MBES', 'category' => 'Personnel', 'name' => 'Personal', 'unit_type' => 'Per Day', 'default_rate' => 4400],
            ['survey_type' => 'MBES', 'category' => 'Personnel', 'name' => 'Surveyor On Board', 'unit_type' => 'Per Day', 'default_rate' => 1200],
            ['survey_type' => 'MBES', 'category' => 'Miscellaneous', 'name' => 'Miscellaneous', 'unit_type' => 'Lump Sum', 'default_rate' => 15000],
            ['survey_type' => 'MBES', 'category' => 'Analysis', 'name' => 'Analysis, Processing, Filtration', 'unit_type' => 'Lump Sum', 'default_rate' => 10000],
            ['survey_type' => 'MBES', 'category' => 'Analysis', 'name' => 'Endorsement from PhN and Land Surveyor', 'unit_type' => 'Lump Sum', 'default_rate' => 6000],
            ['survey_type' => 'MBES', 'category' => 'Analysis', 'name' => 'Report', 'unit_type' => 'Lump Sum', 'default_rate' => 10000],

            // SBES
            ['survey_type' => 'SBES', 'category' => 'Logistics', 'name' => 'Mod and Demod', 'unit_type' => 'Lump Sum', 'default_rate' => 12000],
            ['survey_type' => 'SBES', 'category' => 'Equipment', 'name' => 'Single Beam Rental Packages', 'unit_type' => 'Per Day', 'default_rate' => 1200],
            ['survey_type' => 'SBES', 'category' => 'Equipment', 'name' => 'Boat Rental', 'unit_type' => 'Per Day', 'default_rate' => 3000],
            ['survey_type' => 'SBES', 'category' => 'Logistics', 'name' => 'Tide Gauge and levelling work', 'unit_type' => 'Lump Sum', 'default_rate' => 6000],
            ['survey_type' => 'SBES', 'category' => 'Miscellaneous', 'name' => 'Insurance', 'unit_type' => 'Lump Sum', 'default_rate' => 5000],
            ['survey_type' => 'SBES', 'category' => 'Personnel', 'name' => 'Personal', 'unit_type' => 'Per Day', 'default_rate' => 4350],
            ['survey_type' => 'SBES', 'category' => 'Miscellaneous', 'name' => 'Consumable Item', 'unit_type' => 'Lump Sum', 'default_rate' => 4000],
            ['survey_type' => 'SBES', 'category' => 'Analysis', 'name' => 'Analysis, Processing', 'unit_type' => 'Lump Sum', 'default_rate' => 5000],
            ['survey_type' => 'SBES', 'category' => 'Analysis', 'name' => 'Report', 'unit_type' => 'Lump Sum', 'default_rate' => 5000],
            ['survey_type' => 'SBES', 'category' => 'Analysis', 'name' => 'Endorsement from PhN and Land Surveyor', 'unit_type' => 'Lump Sum', 'default_rate' => 5000],

            // ADCP
            ['survey_type' => 'ADCP', 'category' => 'Logistics', 'name' => 'Mod and Demod', 'unit_type' => 'Lump Sum', 'default_rate' => 15000],
            ['survey_type' => 'ADCP', 'category' => 'Equipment', 'name' => 'ADCP Rental', 'unit_type' => 'Per Day', 'default_rate' => 600],
            ['survey_type' => 'ADCP', 'category' => 'Equipment', 'name' => 'Battery', 'unit_type' => 'Per Day', 'default_rate' => 1500],
            ['survey_type' => 'ADCP', 'category' => 'Equipment', 'name' => 'Boat Rental', 'unit_type' => 'Per Day', 'default_rate' => 3000],
            ['survey_type' => 'ADCP', 'category' => 'Miscellaneous', 'name' => 'Insurance ADCP', 'unit_type' => 'Lump Sum', 'default_rate' => 10000],
            ['survey_type' => 'ADCP', 'category' => 'Personnel', 'name' => 'Personal', 'unit_type' => 'Per Day', 'default_rate' => 6350],
            ['survey_type' => 'ADCP', 'category' => 'Miscellaneous', 'name' => 'Consumable Item', 'unit_type' => 'Lump Sum', 'default_rate' => 5000],
            ['survey_type' => 'ADCP', 'category' => 'Analysis', 'name' => 'Analysis, Processing', 'unit_type' => 'Lump Sum', 'default_rate' => 5000],
            ['survey_type' => 'ADCP', 'category' => 'Analysis', 'name' => 'Report', 'unit_type' => 'Lump Sum', 'default_rate' => 5000],
        ];

        CostRate::truncate(); // Clear old default data

        foreach ($rates as $rate) {
            CostRate::create($rate);
        }
    }
}
