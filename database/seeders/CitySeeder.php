<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cities = ['Madrid', 'Barcelona', 'Ourense', 'Valencia', 'Girona'];

        foreach ($cities as $city) {
            DB::table('cities')->insert([
                'name' => $city,
                'country_id' => 1
            ]);
        }
    }
}
