<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PriceHistory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $startDates = [
            '2021-01-01 21:00:00',
            '2021-01-01 22:00:00',
            '2021-01-01 23:00:00'
        ];

        $amountAugment = 0;

        foreach ($startDates as $startDate) {
            DB::table('price_history')->insert([
                'property_id' => 1,
                'start_date' => $startDate,
                'amount' => 100000 * $amountAugment,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $amountAugment += 0.1;
        }
    }
}
