<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CityUpdateTests extends TestCase
{
    public function test_city_update_customer_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $uri = Config::get('app.url') . '/api/cities/update';

        $payload = [];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User',
            ]);
    }

    public function test_city_update_owner_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('owner');

        $uri = Config::get('app.url') . '/api/cities/update';

        $payload = [];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User',
            ]);
    }

    public function test_city_update_employee_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('employee');

        $uri = Config::get('app.url') . '/api/cities/update';

        $payload = [];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User',
            ]);
    }

    public function test_city_update_invalid_post_data_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/cities/update';

        $payload = [
            'name' => '',
            'country_id' => ''
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_city_update_ok_change_country_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/cities/update';

        $city = City::where('country_id', 1)->get()->first();

        $payload = [
            'id' => $city->id,
            'name' => 'Cities name',
            'country_id' => 2
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('cities', $payload);
    }

    public function test_city_update_ok_change_name_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/cities/update';

        $city = City::where('country_id', 1)->get()->first();

        $payload = [
            'id' => $city->id,
            'name' => 'Cities name',
            'country_id' => $city->country_id
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('cities', $payload);
    }

    public function test_city_update_ok_change_country_and_name_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/cities/update';

        $city = City::where('country_id', 1)->get()->first();

        $payload = [
            'id' => $city->id,
            'name' => 'Cities name',
            'country_id' => 2
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('cities', $payload);
    }
}
