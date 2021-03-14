<?php

namespace Tests\Feature\tests\Feature;

use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CountryUpdateTests extends TestCase
{
    public function test_update_country_customer_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $uri = Config::get('app.url') . '/api/countries/update';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User'
            ]);
    }

    public function test_update_country_invalid_post_data_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/countries/update';

        $payload = [
            'code' => '', // This must be characters
            'name' => 'França'
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_update_country_ok_code_already_exists_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $codeAlreadyExists = Country::inRandomOrder()->first()->code;

        $uri = Config::get('app.url') . '/api/countries/update';

        $payload = [
            'code' => $codeAlreadyExists,
            'name' => 'França' // Update name
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)->dump()
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('countries', $payload);
    }

}
