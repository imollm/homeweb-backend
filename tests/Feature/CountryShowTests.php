<?php

namespace Tests\Feature\tests\Feature;

use App\Models\City;
use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CountryShowTests extends TestCase
{
    public function test_country_show_all()
    {
        $uri = Config::get('app.url') . '/api/countries/index';

        $this->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => Country::all()->toArray(),
                'message' => 'List of all countries',
            ]);
    }

    public function test_country_show_by_id_found()
    {
        $randCountry = Country::inRandomOrder()->first();

        $uri = Config::get('app.url') . '/api/countries/'.$randCountry->id.'/show';

        $this->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => $randCountry->toArray(),
                'message' => 'Country found'
            ]);
    }

    public function test_country_show_by_id_not_found()
    {
        $countryIdNotExists = Country::max('id') + 1;

        $uri = Config::get('app.url') . '/api/countries/'.$countryIdNotExists.'/show';

        $this->getJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'Country not found'
            ]);
    }
}
