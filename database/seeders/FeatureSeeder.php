<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $features = ['jardí', 'piscina', 'rentador', 'vistes', 'wifi', 'aire a condicionat', 'garatge'];

        foreach ($features as $feature) {
            DB::table('features')->insert([
                'name' => $feature,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }
}
