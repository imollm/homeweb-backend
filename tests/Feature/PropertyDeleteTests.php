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

class PropertyDeleteTests extends TestCase
{
    public function test_delete_property_not_found_admin_role()
    {
        $token = $this->getRoleTokenAuth('admin');

        $propertyIdNotExists = Property::max('id') + 1;

        $uri = Config::get('app.url') . '/api/properties/'.$propertyIdNotExists.'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'Property not found'
            ]);
    }

    public function test_delete_property_can_not_be_deleted_admin_role()
    {
        $token = $this->getRoleTokenAuth('admin');

        $propertyIdCanNotBeDeleted =
            Property::join('sales', 'properties.id', '=', 'sales.property_id')
                ->join('tours', 'properties.id', '=', 'tours.property_id')
                ->get()
                ->first()
                ->id;

        $uri = Config::get('app.url') . '/api/properties/'.$propertyIdCanNotBeDeleted.'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'success' => false,
                'message' => 'Property can not be deleted, it info can not be deleted'
            ]);
    }

    public function test_delete_property_ok_admin_role()
    {
        // FIRST CREATE NEW PROPERTY WITHOUT RELATIONS WITH SALES AND TOURS

        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/properties/create';

        $payload = [
            'category_id' => 1,
            'user_id' => null,
            'city_id' => 1,
            'title' => Str::title(10),
            'reference' => Str::random(12),
            'plot_meters' => 100,
            'built_meters' => 90,
            'address' => Str::random(20),
            'longitude' => 10.00,
            'latitude' => 20.00,
            'description' => Str::random(30),
            'energetic_certification' => Arr::random(['obtingut', 'en proces', 'pendent']),
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

        $this->assertDatabaseHas('properties', $payload);

        // SECOND TRY TO DELETE THIS PROPERTY WITHOUT THIS RELATIONS

        $propertyIdCanBeDeleted =
            Property::doesntHave('sales')->doesntHave('tours')->get()->first()->toArray();

        $uri = Config::get('app.url') . '/api/properties/'.$propertyIdCanBeDeleted['id'].'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Property deleted successfully'
            ]);

        $this->assertDatabaseMissing('properties', $propertyIdCanBeDeleted);
    }
}
