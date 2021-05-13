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

        $amountAugment = 1.25;

        for ($i = 0; $i < $howManyPropertiesAre; $i++) {
            $propertyId = ($i + 1);
            foreach ($startDates as $key => $startDate) {
                $amount = 100000 * $amountAugment;
                DB::table('price_history')->insert([
                    'hash_id' => hash("sha256", $propertyId.$startDate.$amount),
                    'property_id' => $propertyId,
                    'start' => $startDate,
                    'amount' => $amount,
                    'end' => ($key < 2) ? $startDates[$key + 1] : null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                $amountAugment += 0.1;
                Property::find($propertyId)->update([
                    'price' => $amount
                ]);
            }
        }
    }
}
