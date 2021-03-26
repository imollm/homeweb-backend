<?php

namespace Tests\Feature;

use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CountryCreateTests extends TestCase
{
    public function test_country_store_customer_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $uri = Config::get('app.url') . '/api/countries/store';

        $payload = [];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_country_store_invalid_post_data_code_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/countries/store';

        $payload = [
            'code' => 'GERMANY', // Invalid, code must be max 3 characters
            'name' => 'Alemanya'
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    }

    public function test_country_store_code_already_exists_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');
        $codeAlreadyExists = Country::inRandomOrder()->first()->code;

        $uri = Config::get('app.url') . '/api/countries/store';

        $payload = [
            'code' => $codeAlreadyExists,
            'name' => 'Some country name'
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function test_country_store_name_already_exists_but_have_new_code_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');
        $nameAlreadyExists = Country::inRandomOrder()->first()->name;

        $uri = Config::get('app.url') . '/api/countries/store';

        $payload = [
            'code' => 'ABC',
            'name' => $nameAlreadyExists
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
                'message' => 'Country created'
            ]);

        $this->assertDatabaseHas('countries', $payload);
    }

    public function test_country_store_ok_role_admin_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/countries/store';

        $payload = [
            'code' => 'GER',
            'name' => 'Alemanya'
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
                'message' => 'Country created'
            ]);

        $this->assertDatabaseHas('countries', $payload);
    }
}
