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

        $payload = [
            'category_id' => 1,
            'user_id' => $ownerUser,
            'city_id' => 1,
            'title' => 1000, // This field is required and must be a STRING not INTEGER
            'reference' => Str::random(12),
            'plot_meters' => 100,
            'built_meters' => 90,
            'address' => Str::random(20),
            'longitude' => 10.00,
            'latitude' => 20.00,
            'description' => Str::random(30),
            'energetic_certification' => Arr::random(['obtained', 'in process', 'pending']),
            'sold' => false,
            'active' => true,
            'price' => 190.000,
        ];

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

        $payload = [
            'category_id' => 1,
            'user_id' => '',
            'city_id' => 1,
            'title' => Str::title(10),
            'reference' => $referenceAlreadyExists, // Reference is unique in DB, now post the same reference in a new property
            'plot_meters' => 100,
            'built_meters' => 90,
            'address' => Str::random(20),
            'longitude' => 10.00,
            'latitude' => 20.00,
            'description' => Str::random(30),
            'energetic_certification' => Arr::random(['obtained', 'in process', 'pending']),
            'sold' => false,
            'active' => true,
            'price' => 190.000,
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_create_new_property_owner_role()
    {
        $token = $this->getRoleTokenAuth('owner');

        $uri = Config::get('app.url') . '/api/properties/create';

        $payload = [
            'category_id' => 1,
            'user_id' => '', // Now this field comes empty
            'city_id' => 1,
            'title' => Str::title(10),
            'reference' => Str::random(12),
            'plot_meters' => 100,
            'built_meters' => 90,
            'address' => Str::random(20),
            'longitude' => 10.00,
            'latitude' => 20.00,
            'description' => Str::random(30),
            'energetic_certification' => Arr::random(['obtained', 'in process', 'pending']),
            'sold' => false,
            'active' => true,
            'price' => 190.000,
        ];

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

        $payload = [
            'category_id' => 1,
            'user_id' => $ownerUser,
            'city_id' => 1,
            'title' => Str::title(10),
            'reference' => Str::random(12),
            'plot_meters' => 100,
            'built_meters' => 90,
            'address' => Str::random(20),
            'longitude' => 10.00,
            'latitude' => 20.00,
            'description' => Str::random(30),
            'energetic_certification' => Arr::random(['obtained', 'in process', 'pending']),
            'sold' => false,
            'active' => true,
            'price' => 190.000,
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
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

        $payload = [
            'category_id' => 1,
            'user_id' => $notOwnerUser, // This is the question, this id is not owner
            'city_id' => 1,
            'title' => Str::title(10),
            'reference' => Str::random(12),
            'plot_meters' => 100,
            'built_meters' => 90,
            'address' => Str::random(20),
            'longitude' => 10.00,
            'latitude' => 20.00,
            'description' => Str::random(30),
            'energetic_certification' => Arr::random(['obtained', 'in process', 'pending']),
            'sold' => false,
            'active' => true,
            'price' => 190.000,
        ];

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

        $payload = [
            'category_id' => 1,
            'user_id' => '',
            'city_id' => 1,
            'title' => Str::title(10),
            'reference' => Str::random(12),
            'plot_meters' => 100,
            'built_meters' => 90,
            'address' => Str::random(20),
            'longitude' => 10.00,
            'latitude' => 20.00,
            'description' => Str::random(30),
            'energetic_certification' => Arr::random(['obtained', 'in process', 'pending']),
            'sold' => false,
            'active' => true,
            'price' => 190.000,
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
                'message' => 'Property added correctly',
            ]);
    }

    public function test_create_new_property_employee_role_with_correct_owner_id()
    {
        $token = $this->getRoleTokenAuth('employee');

        $uri = Config::get('app.url') . '/api/properties/create';

        $ownerRole = Role::where('name', '=', 'owner')->first();
        $ownerUser = User::where('role_id', '=', $ownerRole->id)->first()->id;

        $payload = [
            'category_id' => 1,
            'user_id' => $ownerUser,
            'city_id' => 1,
            'title' => Str::title(10),
            'reference' => Str::random(12),
            'plot_meters' => 100,
            'built_meters' => 90,
            'address' => Str::random(20),
            'longitude' => 10.00,
            'latitude' => 20.00,
            'description' => Str::random(30),
            'energetic_certification' => Arr::random(['obtained', 'in process', 'pending']),
            'sold' => false,
            'active' => true,
            'price' => 190.000,
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
                'message' => 'Property added correctly',
            ]);
    }

    public function test_create_new_property_employee_role_with_incorrect_owner_id()
    {
        $token = $this->getRoleTokenAuth('employee');

        $uri = Config::get('app.url') . '/api/properties/create';

        $notOwnerUser = 1;

        $payload = [
            'category_id' => 1,
            'user_id' => $notOwnerUser, // This is the question, this id is not owner
            'city_id' => 1,
            'title' => Str::title(10),
            'reference' => Str::random(12),
            'plot_meters' => 100,
            'built_meters' => 90,
            'address' => Str::random(20),
            'longitude' => 10.00,
            'latitude' => 20.00,
            'description' => Str::random(30),
            'energetic_certification' => Arr::random(['obtained', 'in process', 'pending']),
            'sold' => false,
            'active' => true,
            'price' => 190.000,
        ];

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
