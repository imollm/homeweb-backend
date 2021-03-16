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

        for ($i = 0; $i < 4; $i++) {
            DB::table('tours')->insert([
                'property_id' => Arr::random($propertiesId),
                'customer_id' => ($i < 2) ? $customersId[0] : $customersId[1],
                'employee_id' => ($i % 2 == 0) ? $employeesId[0] : $employeesId[1],
                'date' => $date,
                'time' => date('H:i:s'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $date = date("Y-m-d", strtotime("$date +1 day"));
        }
    }
}
