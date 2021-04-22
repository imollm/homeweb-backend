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
        $locations = [
            'Madrid' =>     ['latitude' => '40.422198', 'longitude' => '-3.68057'],
            'Barcelona' =>  ['latitude' => '41.368897', 'longitude' => '2.197116'],
            'Ourense' =>    ['latitude' => '42.342633', 'longitude' => '-7.745511'],
            'Valencia' =>   ['latitude' => '39.436535', 'longitude' => '-0.373685'],
            'Girona' =>     ['latitude' => '41.935306', 'longitude' => '2.867282'],
        ];

        foreach ($cities as $city) {
            DB::table('cities')->insert([
                'name' => strtolower($city),
                'country_id' => 1
            ]);
        }
    }
}
