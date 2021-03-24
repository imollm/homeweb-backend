<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $propertiesId = Property::all()->pluck('id')->toArray();
        $customersId = User::join('roles', 'roles.id', '=', 'users.role_id')->where('roles.name', '=', 'customer')->pluck('users.id')->toArray();
        $employeesId = User::join('roles', 'roles.id', '=', 'users.role_id')->where('roles.name', '=', 'employee')->pluck('users.id')->toArray();
        $date = date('Y-m-d');

        foreach ($propertiesId as $index => $propertyId) {

            if ($index % 2 === 0) {

                $buyerId = Arr::random($customersId);
                $sellerId = Arr::random($employeesId);
                $hashId = hash("sha256", $propertyId.$buyerId.$sellerId.$date);

                DB::table('sales')->insert([
                    'property_id' => $propertyId,
                    'buyer_id' => $buyerId,
                    'seller_id' => $sellerId,
                    'date' => $date,
                    'amount' => Property::whereId($propertyId)->pluck('price')->first(),
                    'hash_id' => $hashId,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                $date = date("Y-m-d", strtotime("$date +1 year"));

                Property::find($propertyId)->update(['sold' => true]);

            }
        }
    }
}
