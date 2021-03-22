<?php

namespace Tests\Feature;

use App\Models\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class FeatureUpdateTests extends TestCase
{
    public function test_feature_update_employee_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('employee');

        $uri = Config::get('app.url') . '/api/features/update';

        $payload = [];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_feature_update_customer_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $uri = Config::get('app.url') . '/api/features/update';

        $payload = [];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_feature_update_owner_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('owner');

        $uri = Config::get('app.url') . '/api/features/update';

        $payload = [];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_feature_update_admin_role_feature_not_found()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/features/update';

        $featureNotFound = Feature::max('id') + 1;

        $payload = [
            'id' => $featureNotFound,
            'name' => Str::random(10)
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'Feature not found'
            ]);
    }

    public function test_feature_update_ok_admin_role()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/features/update';

        $featureFound = Feature::inRandomOrder()->first();

        $payload = [
            'id' => $featureFound->id,
            'name' => Str::random(10)
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Feature updated'
            ]);

        $this->assertDatabaseHas('features', $payload);
    }
}
