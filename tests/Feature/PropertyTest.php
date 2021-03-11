<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PropertyTest extends TestCase
{
    public function test_create_new_property_admin_role_with_correct_owner_id()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/properties/create';

        $ownerRole = Role::where('name', '=', 'owner')->first();
        $ownerUser = User::where('role_id', '=', $ownerRole->id)->first()->id;

        $payload = [
            'category_id' => 1,
            'user_id' => $ownerUser,
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

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload);

        $response->dump();

        $response
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

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload);

        $response
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'success' => false,
                'message' => 'Property not added',
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

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload);

        $response
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

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload);

        $response
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'success' => false,
                'message' => 'Property not added',
            ]);
    }

    public function test_create_new_property_owner_role()
    {
        $token = $this->getRoleTokenAuth('owner');

        $uri = Config::get('app.url') . '/api/properties/create';

        $payload = [
            'category_id' => 1,
            'user_id' => '', // Now this field comes empty
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

        $response = $this
                ->withHeader('Authorization', 'Bearer ' . $token)
                ->postJson($uri, $payload);

        $response
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
                'message' => 'Property added correctly'
            ]);
    }

    public function test_create_new_property_customer_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $uri = Config::get('app.url') . '/api/properties/create';

        $payload = [
            'category_id' => 1,
            'user_id' => '', // Now this field comes empty
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

        $response = $this
                        ->withHeader('Authorization', 'Bearer ' . $token)
                        ->postJson($uri, $payload);

        $response
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User',
            ]);
    }

    public function test_update_property_does_not_exists()
    {
        $token = $this->getRoleTokenAuth('admin');

        $propertyNotExists = 1000;

        $uri = Config::get('app.url') . '/api/properties/'.$propertyNotExists.'/update';

        $payload = [
            'category_id' => 3
         ];

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload);

        $response
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'Property not found'
            ]);

    }

    public function test_update_property_admin_role_property_exists()
    {
        $token = $this->getRoleTokenAuth('admin');
        $randomProperty = Property::inRandomOrder()->first()->id;

        $uri = Config::get('app.url') . '/api/properties/'.$randomProperty.'/update';

        $payload = [
            'category_id' => 4,
            'user_id' => 3,
            'title' => Str::random(10),
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
            'price' => 12498.45,
        ];

        $response = $this
                ->withHeader('Authorization', 'Bearer ' . $token)
                ->putJson($uri, $payload);

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Property updated successfully',
            ]);

        $this->assertDatabaseHas('properties', $payload);
    }

    public function test_update_property_employee_role_property_exists()
    {
        $token = $this->getRoleTokenAuth('employee');
        $randomProperty = Property::inRandomOrder()->first()->id;

        $uri = Config::get('app.url') . '/api/properties/'.$randomProperty.'/update';

        $payload = [
            'category_id' => 4,
            'user_id' => 3,
            'title' => Str::random(10),
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

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload);

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Property updated successfully'
            ]);

        $this->assertDatabaseHas('properties', $payload);
    }

    public function test_update_property_owner_role_and_he_is_the_owner()
    {
        $token = $this->getRoleTokenAuth('owner');

        $ownerUserId = User::where('name', '=', 'Owner')->first()->id;
        $myProperty =  Property::where('user_id', '=', $ownerUserId)->first()->id;

        $uri = Config::get('app.url').'/api/properties/'.$myProperty.'/update';

        $payload = [
            'category_id' => 4,
            'user_id' => $ownerUserId,
            'title' => Str::random(10),
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

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Property updated successfully',
            ]);

        $this->assertDatabaseHas('properties', $payload);
    }

    public function test_update_property_owner_role_not_owner()
    {
        // Owner1 wants to update a property that owns Owner

        $token = $this->getRoleTokenAuth('owner1');

        $ownerUserId = User::where('name', '=', 'owner')->first()->id;
        $myProperty =  Property::where('user_id', '=', $ownerUserId)->first()->id;

        $uri = Config::get('app.url').'/api/properties/'.$myProperty.'/update';

        $payload = [
            'category_id' => 4,
            'user_id' => $ownerUserId,
            'title' => Str::random(10),
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

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User',
            ]);
    }
}
