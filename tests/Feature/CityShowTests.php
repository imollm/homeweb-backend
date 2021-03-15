<?php

namespace Tests\Feature;

use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CityShowTests extends TestCase
{
    public function test_city_show_all()
    {
        $uri = Config::get('app.url') . '/api/cities/index';

        $this->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => City::all()->toArray(),
                'message' => 'List of all cities',
            ]);
    }

    public function test_city_show_by_id_found()
    {
        $randCity = City::inRandomOrder()->first();

        $uri = Config::get('app.url') . '/api/cities/'.$randCity->id.'/show';

        $this->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => $randCity->toArray(),
                'message' => 'City found'
            ]);
    }

    public function test_country_show_by_id_not_found()
    {
        $cityIdNotExists = City::max('id') + 1;

        $uri = Config::get('app.url') . '/api/cities/'.$cityIdNotExists.'/show';

        $this->getJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'City not found'
            ]);
    }
}
