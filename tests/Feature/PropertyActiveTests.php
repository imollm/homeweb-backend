<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PropertyActiveTests extends TestCase
{
    public function test_property_set_active_a_property_that_not_exists_with_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $randPropertyId = 1000; // This property not exists

        $uri = Config::get('app.url') . '/api/properties/'.$randPropertyId.'/setActive/1';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'Property not found',
            ]);
    }

    public function test_property_set_active_a_inactive_property_with_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $randPropertyId = Property::inRandomOrder()->where('active', false)->first()->id;
        $active = 1;

        $uri = Config::get('app.url') . '/api/properties/'.$randPropertyId.'/setActive/' . $active;

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Visibility was toggled'
            ]);

        $this->assertDatabaseHas('properties', [
            'id' => $randPropertyId,
            'active' => $active,
        ]);
    }

    public function test_property_set_inactive_an_active_property_with_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $randPropertyId = Property::inRandomOrder()->where('active', false)->first()->id;
        $inactive = 0;

        $uri = Config::get('app.url') . '/api/properties/'.$randPropertyId.'/setActive/' . $inactive;

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Visibility was toggled'
            ]);

        $this->assertDatabaseHas('properties', [
            'id' => $randPropertyId,
            'active' => $inactive,
        ]);
    }

}
