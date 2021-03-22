<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeaturePropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $properties = Property::all()->toArray();
        $features = Feature::all()->toArray();

        unset($features[0]);

        foreach ($properties as $property) {

            foreach ($features as $feature) {

                if (rand(0,1) == 1) {

                    DB::table('feature_property')->insert([
                        'feature_id' => $feature['id'],
                        'property_id' => $property['id'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);

                }
            }
        }
    }
}
