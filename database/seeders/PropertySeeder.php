<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $energetic_certification_values = ['obtingut', 'en proces', 'pendent'];
        $ownersIdsOnSystem =
            DB::table('roles')
                ->join('users', 'roles.id', '=', 'users.role_id')
                ->where('roles.name', 'owner')
                ->pluck('users.id')->toArray();

        for($i = 0; $i < 5; $i++) {
            DB::table('properties')->insert([
                'user_id' => Arr::random($ownersIdsOnSystem),
                'category_id' => $i === 0 ? 2 : $i + 1,
                'city_id' => City::inRandomOrder()->first()->id,
                'title' => Str::random(10),
                'reference' => strtolower(Str::random(6)),
                'image' => 'property' . ($i + 1) . '.jpg',
                'plot_meters' => $i * 100,
                'built_meters' => $i * 90,
                'rooms' => 3,
                'baths' => 2,
                'address' => Str::random(20),
                'longitude' => '40.004676',
                'latitude' => '3.835806',
                'description' => Str::random(100),
                'energetic_certification' => Arr::random($energetic_certification_values),
                'sold' => ($i === 0),
                'active' => $i % 2 == 0,
                'price' => 100000 * ($i+1),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
