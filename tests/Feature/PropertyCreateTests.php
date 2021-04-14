<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PropertyCreateTests extends TestCase
{
    private function getPayload(array $values = []): array
    {
        $payload = [
            'user_id' => null,
            'category_id' => 1,
            'city_id' => 1,
            'title' => Str::random(10),
            'reference' => Str::random(12),
            'image' => 'image.jpg',
            'plot_meters' => 100,
            'built_meters' => 90,
            'rooms' => 3,
            'baths' => 2,
            'address' => Str::random(20),
            'longitude' => 10.00,
            'latitude' => 20.00,
            'description' => Str::random(30),
            'energetic_certification' => Arr::random(['obtingut', 'en proces', 'pendent']),
            'sold' => false,
            'active' => true,
            'price' => 190.000,
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

    public function test_create_new_property_customer_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $uri = Config::get('app.url') . '/api/properties/create';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User',
            ]);
    }

    public function test_create_new_property_admin_role_invalid_post_data_title()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/properties/create';

        $ownerRole = Role::where('name', '=', 'owner')->first();
        $ownerUser = User::where('role_id', '=', $ownerRole->id)->first()->id;

        // title must be a string, integer given
        $payload = $this->getPayload(['title' => 1000, 'user_id' => $ownerUser]);

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_create_new_property_admin_role_reference_unique_already_exists()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/properties/create';

        $referenceAlreadyExists = Property::inRandomOrder()->first()->reference;

        // Reference is unique in DB, now post with the same reference in a new property
        $payload = $this->getPayload(['reference' => $referenceAlreadyExists]);

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_create_new_property_owner_role()
    {
        $token = $this->getRoleTokenAuth('owner');

        $uri = Config::get('app.url') . '/api/properties/create';

        // Now user_id field comes empty
        $payload = $this->getPayload(['user_id' => '']);

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
                'message' => 'Property added correctly'
            ]);
    }

    public function test_create_new_property_admin_role_with_correct_owner_id()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/properties/create';

        $ownerRole = Role::where('name', '=', 'owner')->first();
        $ownerUser = User::where('role_id', '=', $ownerRole->id)->first()->id;

        $payload = $this->getPayload(['user_id' => $ownerUser]);

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)->dump()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
                'message' => 'Property added correctly',
            ]);
    }

    public function test_create_new_property_admin_role_with_incorrect_owner_id()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/properties/create';

        $notOwnerUser = 1;

        // This is the question, this id is not owner
        $payload = $this->getPayload(['user_id' => $notOwnerUser]);

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'success' => false,
                'message' => 'Property not added',
            ]);
    }

    public function test_create_new_property_admin_role_without_owner_id()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/properties/create';

        // Owner id to null
        $payload = $this->getPayload(['user_id' => null]);

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
                'message' => 'Property added correctly',
            ]);

        $this->assertDatabaseHas('properties', $payload);
    }

    public function test_create_new_property_employee_role_with_correct_owner_id()
    {
        $token = $this->getRoleTokenAuth('employee');

        $uri = Config::get('app.url') . '/api/properties/create';

        $ownerRole = Role::where('name', '=', 'owner')->first();
        $ownerUser = User::where('role_id', '=', $ownerRole->id)->first()->id;

        $payload = $this->getPayload(['user_id' => $ownerUser]);

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
                'message' => 'Property added correctly',
            ]);

        $this->assertDatabaseHas('properties', $payload);
    }

    public function test_create_new_property_employee_role_with_incorrect_owner_id()
    {
        $token = $this->getRoleTokenAuth('employee');

        $uri = Config::get('app.url') . '/api/properties/create';

        $notOwnerUser = 1;

        // This is the question, this id is not owner
        $payload = $this->getPayload(['user_id' => $notOwnerUser]);

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'success' => false,
                'message' => 'Property not added',
            ]);
    }
}
