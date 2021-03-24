<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
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
                'message' => 'Property can not be deleted'
            ]);
    }

    public function test_delete_property_ok_admin_role()
    {
        $token = $this->getRoleTokenAuth('admin');

        $propertyIdCanBeDeleted =
            Property::doesntHave('sales')->doesntHave('tours')->get()->first()->toArray();

        $uri = Config::get('app.url') . '/api/properties/'.$propertyIdCanBeDeleted['id'].'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('properties', $propertyIdCanBeDeleted);
    }
}
