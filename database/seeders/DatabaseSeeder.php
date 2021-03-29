<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CategorySeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            CountrySeeder::class,
            CitySeeder::class,
            PropertySeeder::class,
            RangePriceSeeder::class,
            PriceHistorySeeder::class,
            TourSeeder::class,
            SaleSeeder::class
        ]);
    }
}
