<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = ['Casa', 'Xalet', 'Apartament', 'Finca', 'Masia'];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'name' => strtolower($category),
                'image' => strtolower($category) . '.jpg',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
