<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Property;
use App\Models\RangePrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PropertyShowTests extends TestCase
{
    public function test_show_all_properties()
    {
        $uri = Config::get('app.url') . '/api/properties/index';

        $activeProperties = Property::whereActive(true)->get()->toArray();

        $this
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => $activeProperties,
                'message' => 'List of all properties'
            ]);
    }

    public function test_show_property_by_id()
    {
        $randomPropertyId = Property::inRandomOrder()->first()->id;

        $uri = Config::get('app.url') . '/api/properties/'.$randomPropertyId.'/show';

        $this
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => array(),
                'message' => 'The property was request',
            ]);
    }

    public function test_show_property_by_id_not_exists()
    {
        $randomPropertyId = 1000; // This property ID not exists

        $uri = Config::get('app.url') . '/api/properties/'.$randomPropertyId.'/show';

        $this
            ->getJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'Property not found'
            ]);
    }

    public function test_show_property_by_filter_reference()
    {
        $uri = Config::get('app.url') . '/api/properties/showByFilter';

        $randomPropertyReference = Property::inRandomOrder()->first()->reference;

        $uri = $uri . '?reference=' . $randomPropertyReference;

        $this
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => array(),
                'message' => 'Properties request'
            ]);
    }

    public function test_show_property_by_filter_price()
    {
        $range = '100.000 a 200.000';
        $rangeId = RangePrice::where('value', $range)->first()->id;

        $uri = Config::get('app.url') . '/api/properties/showByFilter';

        $uri = $uri . '?price=' . $rangeId;

        $this
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => array(),
                'message' => 'Properties request'
            ]);
    }

    public function test_show_property_by_filter_city()
    {
        $cityId = Property::inRandomOrder()->first()->city_id;

        $uri = Config::get('app.url') . '/api/properties/showByFilter';

        $uri = $uri . '?location=' . $cityId;

        $this
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => array(),
                'message' => 'Properties request',
            ]);
    }

    public function test_show_property_by_filter_category()
    {
        $categoryId = Property::inRandomOrder()->first()->category_id;

        $uri = Config::get('app.url') . '/api/properties/showByFilter';

        $uri = $uri . '?category=' . $categoryId;

        $this
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => array(),
                'message' => 'Properties request',
            ]);
    }

    public function test_show_property_by_filters_that_not_have_properties_with_this_values()
    {
        $categoryIdWithNotPropertyRelated =
            DB::table('categories')
            ->select('categories.id')
            ->leftJoin('properties', 'categories.id', '=', 'properties.category_id')
            ->whereNull('properties.category_id')
            ->first()
            ->id;

        $randomPropertyReference = Property::inRandomOrder()->first()->reference;

        $uri = Config::get('app.url') . '/api/properties/showByFilter';

        $uri .= '?reference='.$randomPropertyReference.'&category=' . $categoryIdWithNotPropertyRelated;

        $this
            ->getJson($uri)->dump()
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_show_property_by_all_filters()
    {
        $randPropertyReference = Property::inRandomOrder()->first()->reference;
        $randRangePriceId = RangePrice::inRandomOrder()->first()->id;
        $randCityId = City::inRandomOrder()->first()->id;
        $randCategoryId = Category::inRandomOrder()->first()->id;


        $uri = Config::get('app.url') . '/api/properties/showByFilter';

        $uri = $uri.'?reference='.$randPropertyReference.'&price='.$randRangePriceId.'&location='.$randCityId.'&category='.$randCategoryId;

        $response = $this->getJson($uri);

        if ($response->status() === Response::HTTP_OK) {
            $response->assertStatus(Response::HTTP_OK)
                ->assertJson([
                    'success' => true,
                    'data' => array(),
                    'message' => 'Properties request',
                ]);
        } elseif ($response->status() === Response::HTTP_NO_CONTENT) {
            $response->assertStatus(Response::HTTP_NO_CONTENT);
        }
    }

    public function test_show_property_by_no_filters()
    {
        $uri = Config::get('app.url') . '/api/properties/showByFilter';

        $uri = $uri.'?reference=&price=&location=&category=';

        $this
            ->getJson($uri)->dump()
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }

}
