<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $propertiesId = Property::all()->pluck('id')->toArray();
        $employeesId = User::join('roles', 'users.role_id', '=', 'roles.id')->where('roles.name', 'employee')->pluck('users.id')->toArray();
        $customersId = User::join('roles', 'users.role_id', '=', 'roles.id')->where('roles.name', 'customer')->pluck('users.id')->toArray();
        $date = date('Y-m-d');
        $time = date('H:i:s', strtotime('10:00:00'));

        for ($i = 0; $i < count($propertiesId) - 1; $i++) {

            $propertyId = $propertiesId[$i];
            $customerId = ($i < 2) ? $customersId[0] : $customersId[1];
            $employeeId = ($i % 2 == 0) ? $employeesId[0] : $employeesId[1];
            $hashId = hash("sha256", $propertyId.$customerId.$employeeId.$date.$time);

            DB::table('tours')->insert([
                'property_id' => $propertyId,
                'customer_id' => $customerId,
                'employee_id' => $employeeId,
                'date' => $date,
                'time' => $time,
                'hash_id' => $hashId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $date = date("Y-m-d", strtotime("$date +1 day"));
            $time = date('H:i:s', strtotime($time) + (60*60));

        }
    }
}
