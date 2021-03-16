<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PriceHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $startDates = [
            '2021-01-01',
            '2021-01-02',
            '2021-01-03'
        ];

        $howManyPropertiesAre = Property::count();

        $amountAugment = 0;

        for ($i = 0; $i < $howManyPropertiesAre; $i++) {
            foreach ($startDates as $key => $startDate) {
                DB::table('price_history')->insert([
                    'property_id' => ($i + 1),
                    'start' => $startDate,
                    'amount' => 100000 * $amountAugment,
                    'end' => ($key < 2) ? $startDates[$key + 1] : null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                $amountAugment += 0.1;
            }
        }
    }
}
