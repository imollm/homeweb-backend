<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PropertyUpdateTests extends TestCase
{
    public function test_update_property_does_not_exists()
    {
        $token = $this->getRoleTokenAuth('admin');

        $propertyNotExists = 1000;

        $uri = Config::get('app.url') . '/api/properties/'.$propertyNotExists.'/update';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'Property not found'
            ]);
    }

    public function test_update_property_customer_role_not_authorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $randomPropertyId = Property::inRandomOrder()->first()->id;

        $uri = Config::get('app.url'). '/api/properties/'.$randomPropertyId.'/update';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User',
            ]);
    }

    public function test_update_property_admin_role_invalid_post_data_reference()
    {
        $token = $this->getRoleTokenAuth('admin');

        $randomPropertyId = Property::inRandomOrder()->first()->id;

        $uri = Config::get('app.url') . '/api/properties/'.$randomPropertyId.'/update';

        $payload = [
            'category_id' => 4,
            'user_id' => 3,
            'title' => Str::random(10),
            'reference' => 99999, // This field must be STRING not INTEGER
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

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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
