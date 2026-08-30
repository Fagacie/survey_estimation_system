<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CostRate;

class CostRateSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            ['category' => 'Logistics', 'name' => 'Mod and Demod', 'unit_type' => 'Lump Sum', 'base_multiplier' => 'MOB/DEMOB Days', 'default_rate' => 12000],
            ['category' => 'Equipment', 'name' => 'Single Beam Rental Packages', 'unit_type' => 'Per Day', 'base_multiplier' => 'Execution Days', 'default_rate' => 1200],
            ['category' => 'Equipment', 'name' => 'Boat Rental', 'unit_type' => 'Per Day', 'base_multiplier' => 'Total Duration', 'default_rate' => 3000],
            ['category' => 'Logistics', 'name' => 'Tide Gauge and levelling work', 'unit_type' => 'Lump Sum', 'base_multiplier' => 'Total Duration', 'default_rate' => 6000],
            ['category' => 'Miscellaneous', 'name' => 'Insurance', 'unit_type' => 'Lump Sum', 'base_multiplier' => 'Total Duration', 'default_rate' => 5000],
            ['category' => 'Personnel', 'name' => 'Personal', 'unit_type' => 'Per Day', 'base_multiplier' => 'Execution Days', 'default_rate' => 4350],
            ['category' => 'Miscellaneous', 'name' => 'Consumable Item', 'unit_type' => 'Lump Sum', 'base_multiplier' => 'Total Duration', 'default_rate' => 4000],
            ['category' => 'Analysis', 'name' => 'Analysis, Processing', 'unit_type' => 'Lump Sum', 'base_multiplier' => 'Total Duration', 'default_rate' => 5000],
            ['category' => 'Analysis', 'name' => 'Report', 'unit_type' => 'Lump Sum', 'base_multiplier' => 'Total Duration', 'default_rate' => 5000],
            ['category' => 'Analysis', 'name' => 'Endorsement from PhN and Land Surveyor', 'unit_type' => 'Lump Sum', 'base_multiplier' => 'Total Duration', 'default_rate' => 5000],
        ];

        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        CostRate::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // Clear old default data

        foreach ($rates as $rate) {
            CostRate::create($rate);
        }
    }
}
