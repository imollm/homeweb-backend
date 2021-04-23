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
    private function getPayload(array $values = []): array
    {
        $payload = [
            'code' => 'DEFAULT',
            'name' => 'DEF',
            'latitude' => '-3.93049574586',
            'longitude' => '55.23472897347'
        ];

        foreach ($values as $keyValues => $valueToChange) {
            foreach ($payload as $keyPayload => $payloadValue) {
                if ($keyValues === $keyPayload) {
                    $payload[$keyPayload] = $valueToChange;
                    break;
                }
            }
        }
        return $payload;
    }

    public function test_country_create_customer_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $uri = Config::get('app.url') . '/api/countries/create';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $this->getPayload())
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_country_create_invalid_post_data_code_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/countries/create';

        $payload = $this->getPayload(['code' => 'GERMANY']); // Invalid code, mus the 3 characters.

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    }

    public function test_country_create_code_already_exists_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');
        $codeAlreadyExists = Country::inRandomOrder()->first()->code;

        $uri = Config::get('app.url') . '/api/countries/create';

        $payload = $this->getPayload(['code' => $codeAlreadyExists]);

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_country_create_name_already_exists_but_have_new_code_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');
        $nameAlreadyExists = Country::inRandomOrder()->first()->name;

        $uri = Config::get('app.url') . '/api/countries/create';

        $payload = $this->getPayload(['name' => $nameAlreadyExists, 'code' => 'AAA']);

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

    public function test_country_create_ok_role_admin_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/countries/create';

        $payload = $this->getPayload([
            'code' => 'GER',
            'name' => 'Alemanya'
        ]);

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
