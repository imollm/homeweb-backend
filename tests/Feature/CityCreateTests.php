<?php

namespace Tests\Feature;

use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CityCreateTests extends TestCase
{
    public function test_city_create_customer_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $uri = Config::get('app.url') . '/api/cities/create';

        $payload = [
            'name' => Str::random(10),
            'country_id' => Country::inRandomOrder()->first()->id
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User',
            ]);
    }

    public function test_city_create_owner_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('owner');

        $uri = Config::get('app.url') . '/api/cities/create';

        $payload = [
            'name' => Str::random(10),
            'country_id' => Country::inRandomOrder()->first()->id
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User',
            ]);
    }

    public function test_city_create_employee_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('employee');

        $uri = Config::get('app.url') . '/api/cities/create';

        $payload = [
            'name' => Str::random(10),
            'country_id' => Country::inRandomOrder()->first()->id
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User',
            ]);
    }

    public function test_city_create_invalid_post_data_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/cities/create';

        $payload = [
            'name' => '',
            'country_id' => Country::inRandomOrder()->first()->id
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_city_create_country_not_found_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/cities/create';

        $cityIdNotExists = Country::max('id') + 1;

        $payload = [
            'name' => 'New City',
            'country_id' => $cityIdNotExists
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'Related country not found'
            ]);
    }

    public function test_city_create_already_exists_with_same_country_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/cities/create';

        $cityAlreadyExists =
            DB::table('cities')
                ->select('cities.name', 'cities.country_id')
                ->join('countries', 'cities.country_id','=', 'countries.id')
                ->first();

        $payload = [
            'name' => $cityAlreadyExists->name,
            'country_id' => $cityAlreadyExists->country_id
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson([
                'success' => false,
                'message' => 'City already exists with same country'
            ]);
    }

    public function test_city_create_ok_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/cities/create';

        $countryIdExists = Country::inRandomOrder()->first()->id;

        $payload = [
            'name' => Str::random(8),
            'country_id' => $countryIdExists
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
                'message' => 'City created'
            ]);

        $this->assertDatabaseHas('cities', $payload);
    }
}
