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
    private function getPayload(array $values = []): array
    {
        $payload = [
            'id' => null,
            'user_id' => null,
            'category_id' => 1,
            'city_id' => 1,
            'title' => Str::random(10),
            'reference' => Str::random(12),
            'image' => null,
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

    public function test_update_property_does_not_exists()
    {
        $token = $this->getRoleTokenAuth('admin');

        $propertyNotExists = 1000;

        $uri = Config::get('app.url') . '/api/properties/update';

        $payload = $this->getPayload(['id' => $propertyNotExists]);

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'Property not found'
            ]);
    }

    public function test_update_property_customer_role_not_authorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $randomProperty = Property::inRandomOrder()->first();

        $uri = Config::get('app.url'). '/api/properties/update';

        $payload = $this->getPayload($randomProperty->toArray());

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User',
            ]);
    }

    public function test_update_property_admin_role_property_exists()
    {
        $token = $this->getRoleTokenAuth('admin');

        $randomProperty = Property::inRandomOrder()->first();

        $uri = Config::get('app.url') . '/api/properties/update';

        $payload = $this->getPayload($randomProperty->toArray());
        $payload['category_id'] = 5;
        $payload['title'] = Str::random(10);

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
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
        $randomProperty = Property::inRandomOrder()->first();

        $uri = Config::get('app.url') . '/api/properties/update';

        $payload = $this->getPayload($randomProperty->toArray());
        $payload['category_id'] = 4;
        $payload['title'] = Str::random(10);

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
        $myProperty =  Property::where('user_id', '=', $ownerUserId)->first();

        $uri = Config::get('app.url').'/api/properties/update';

        $payload = $this->getPayload($myProperty->toArray());
        $payload['category_id'] = 2;
        $payload['title'] = Str::random(10);

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)->dump()
            ->assertStatus(Response::HTTP_OK)
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
        $notMyProperty =  Property::where('user_id', '=', $ownerUserId)->first();

        $uri = Config::get('app.url').'/api/properties/update';

        $payload = $this->getPayload($notMyProperty->toArray());
        $payload['user_id'] = $ownerUserId;

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
