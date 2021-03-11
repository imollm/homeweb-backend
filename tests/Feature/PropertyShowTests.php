<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\RangePrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PropertyShowTests extends TestCase
{
    public function test_show_all_properties()
    {
        $uri = Config::get('app.url') . '/api/properties/all';

        $this
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => array(),
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

        $payload = [
            'reference' => $randomPropertyReference,
            'price' => '',
            'location' => '',
            'category' => ''
        ];

        $this
            ->postJson($uri, $payload)->dump()
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Properties request'
            ]);
    }

    public function test_show_property_by_filter_price()
    {
        $range = '100.000 a 200.000';
        $rangeId = RangePrice::where('range', $range)->first()->id;

        $uri = Config::get('app.url') . '/api/properties/showByFilter';

        $payload = [
            'reference' => '',
            'price' => $rangeId,
            'location' => '',
            'category' => ''
        ];

        $this
            ->postJson($uri, $payload)->dump()
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Properties request'
            ]);
    }
}
