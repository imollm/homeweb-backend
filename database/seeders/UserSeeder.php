<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Customer',
            'email' => 'customer@homeweb.com',
            'password' => bcrypt('12345678'),
            'phone' => '123123123',
            'address' => 'Street XYZ',
            'fiscal_id' => '12345678A',
            'role_id' => 3,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@homeweb.com',
            'password' => bcrypt('12345678'),
            'phone' => '890890890',
            'address' => 'Street ZYX',
            'fiscal_id' => '87654321A',
            'role_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('users')->insert([
            'name' => 'Owner',
            'email' => 'owner@homeweb.com',
            'password' => bcrypt('12345678'),
            'phone' => '909090909',
            'address' => 'Street ABC',
            'fiscal_id' => '11111111Z',
            'role_id' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('users')->insert([
            'name' => 'Employee',
            'email' => 'employee@homeweb.com',
            'password' => bcrypt('12345678'),
            'phone' => '121212121',
            'address' => 'Street CBA',
            'fiscal_id' => '00000000M',
            'role_id' => 4,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('users')->insert([
            'name' => 'Owner1',
            'email' => 'owner1@homeweb.com',
            'password' => bcrypt('12345678'),
            'phone' => '9090909091',
            'address' => 'Street ABC6',
            'fiscal_id' => '11111111Zg',
            'role_id' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
