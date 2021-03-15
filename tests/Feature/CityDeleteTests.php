<?php

namespace Tests\Feature;

use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
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

    public function test_city_delete_id_not_found_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('employee');

        $cityId = City::max('id') + 1;

        $uri = Config::get('app.url') . '/api/cities/'.$cityId.'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User',
            ]);
    }
}
