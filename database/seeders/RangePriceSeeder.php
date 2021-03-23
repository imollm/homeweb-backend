<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RangePriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('range_prices')->insert([
            'value' => '100.000 a 200.000',
            'big_price' => 200000,
            'small_price' => 100000,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('range_prices')->insert([
            'value' => '200.000 a 300.000',
            'big_price' => 300000,
            'small_price' => 200000,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('range_prices')->insert([
            'value' => '300.000 a 400.000',
            'big_price' => 400000,
            'small_price' => 300000,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('range_prices')->insert([
            'value' => '400.000 a 500.000',
            'big_price' => 500000,
            'small_price' => 400000,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
