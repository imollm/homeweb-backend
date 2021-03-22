<?php

namespace Tests\Feature;

use App\Models\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class FeatureDeleteTests extends TestCase
{
    public function test_feature_delete_employee_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('employee');

        $featureId = Feature::inRandomOrder()->first()->id;

        $uri = Config::get('app.url') . '/api/features/'.$featureId.'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_feature_delete_customer_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $featureId = Feature::inRandomOrder()->first()->id;

        $uri = Config::get('app.url') . '/api/features/'.$featureId.'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_feature_delete_owner_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('owner');

        $featureId = Feature::inRandomOrder()->first()->id;

        $uri = Config::get('app.url') . '/api/features/'.$featureId.'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_feature_delete_admin_role_feature_not_found()
    {
        $token = $this->getRoleTokenAuth('admin');

        $featureIdNotExists = Feature::max('id') + 1;

        $uri = Config::get('app.url') . '/api/features/'.$featureIdNotExists.'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson([
                'success' => false,
                'message' => 'This feature can not be deleted'
            ]);
    }

    public function test_feature_delete_admin_role_feature_have_relations_with_properties()
    {
        $token = $this->getRoleTokenAuth('admin');

        $featureIdWithRelations =
            Feature::has('properties')->get()->pluck('id')->first();

        $uri = Config::get('app.url') . '/api/features/'.$featureIdWithRelations.'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson([
                'success' => false,
                'message' => 'This feature can not be deleted'
            ]);
    }

    public function test_feature_delete_ok_admin_role()
    {
        $token = $this->getRoleTokenAuth('admin');

        $featureHaveNotProperties =
            Feature::doesntHave('properties')->get()->first();

        $uri = Config::get('app.url') . '/api/features/'.$featureHaveNotProperties->id.'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Feature deleted'
            ]);

        $this->assertDatabaseMissing('features', $featureHaveNotProperties->toArray());
    }
}
