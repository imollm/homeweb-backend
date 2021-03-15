<?php

namespace Tests\Feature;

use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CityDeleteTests extends TestCase
{
    public function test_city_update_customer_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $cityId = City::inRandomOrder()->first()->id;

        $uri = Config::get('app.url') . '/api/cities/'.$cityId.'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User',
            ]);
    }

    public function test_city_update_owner_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('owner');

        $cityId = City::inRandomOrder()->first()->id;

        $uri = Config::get('app.url') . '/api/cities/'.$cityId.'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User',
            ]);
    }

    public function test_city_update_employee_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('employee');

        $cityId = City::inRandomOrder()->first()->id;

        $uri = Config::get('app.url') . '/api/cities/'.$cityId.'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User',
            ]);
    }

    public function test_city_delete_not_found_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $cityId = City::max('id') + 1;

        $uri = Config::get('app.url') . '/api/cities/'.$cityId.'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'City not found'
            ]);
    }

    public function test_city_delete_has_related_properties_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $cityId = DB::table('cities')
                        ->select('cities.id')
                        ->join('properties', 'cities.id', '=', 'properties.city_id')
                        ->first()
                        ->id;

        $uri = Config::get('app.url') . '/api/cities/'.$cityId.'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson([
                'success' => false,
                'message' => 'Error, this city has properties related'
            ]);
    }

    public function test_city_delete_ok_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $cityWithOutPropertiesRelated =
            DB::table('cities')
                ->select('cities.*')
                ->leftJoin('properties', 'cities.id', '=', 'properties.city_id')
                ->whereNull('properties.city_id')
                ->get()->first();

        $cityId = $cityWithOutPropertiesRelated->id;
        $cityName = $cityWithOutPropertiesRelated->name;

        $city = [
            'id' => $cityId,
            'name' => $cityName
        ];

        $uri = Config::get('app.url') . '/api/cities/'.$cityId.'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('cities', $city);
    }
}
