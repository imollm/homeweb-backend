<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 0; $i < 5; $i++) {
            DB::table('properties')->insert([
                'user_id' => 1,
                'reference' => Str::random(6),
                'plot_meters' => $i * 100,
                'address' => Str::random(20),
                'location' => '{}',
                'active' => true,
                'description' => Str::random(100),
            ]);
        }
    }
}
