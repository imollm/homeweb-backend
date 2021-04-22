<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('countries')->insert([
            'code' => 'ESP',
            'name' => 'Espanya',
            'longitude' => '-2.542626',
            'latitude' => '39.939697',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('countries')->insert([
            'code' => 'ITA',
            'name' => 'Italia',
            'longitude' => '13.651222',
            'latitude' => '41.587148',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
