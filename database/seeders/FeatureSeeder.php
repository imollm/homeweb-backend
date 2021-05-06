<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Property;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $features = ['Piscina', 'Jardí', 'Aire condicionat', 'Xemeneia', 'Vistes', 'Garatge', 'Pàrquing', 'Wifi'];

        foreach ($features as $feature) {
            Feature::create(['name' => $feature]);
        }
    }
}
