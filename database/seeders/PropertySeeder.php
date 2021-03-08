<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
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
        $energetic_certification_values = ['obtained', 'in process', 'pending'];

        for($i = 0; $i < 5; $i++) {
            DB::table('properties')->insert([
                'user_id' => 3,
                'category_id' => $i + 1,
                'title' => Str::random(10),
                'reference' => Str::random(6),
                'plot_meters' => $i * 100,
                'built_meters' => $i * 90,
                'address' => Str::random(20),
                'location' => '{}',
                'description' => Str::random(100),
                'energetic_certification' => Arr::random($energetic_certification_values),
                'sold' => false,
                'active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
