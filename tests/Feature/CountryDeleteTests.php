<?php

namespace Tests\Feature;

use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CountryDeleteTests extends TestCase
{
    public function test_delete_country_customer_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $countryId = Country::inRandomOrder()->first()->id;

        $uri = Config::get('app.url') . '/api/countries/'.$countryId.'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User'
            ]);
    }

    public function test_delete_country_not_found_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $countryIdNotExists = Country::max('id') + 1;

        $uri = Config::get('app.url') . '/api/countries/'.$countryIdNotExists.'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'The country can not be found'
            ]);
    }

    public function test_delete_country_has_related_cities_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $countryIdWithRelatedCities =
            DB::table('countries')
            ->select('countries.*')
            ->leftJoin('cities', 'countries.id', '=', 'cities.country_id')
            ->get()->first()->id;

        $uri = Config::get('app.url') . '/api/countries/'.$countryIdWithRelatedCities.'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson([
                'success' => false,
                'message' => 'The country have cities related'
            ]);
    }

    public function test_delete_country_ok_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $countryWithOutRelationsWithCities =
            DB::table('countries')
                ->select('countries.*')
                ->leftJoin('cities', 'countries.id', '=', 'cities.country_id')
                ->whereNull('cities.country_id')
                ->get()->first();

        $countryId = $countryWithOutRelationsWithCities->id;
        $countryName = $countryWithOutRelationsWithCities->name;

        $country = [
            'id' => $countryId,
            'name' => $countryName
        ];

        $uri = Config::get('app.url') . '/api/countries/'.$countryId.'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('countries', $country);
    }

}
