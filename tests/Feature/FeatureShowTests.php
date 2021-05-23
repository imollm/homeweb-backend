<?php

namespace Tests\Feature;

use App\Models\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class FeatureShowTests extends TestCase
{
    public function test_feature_show_index_customer_role()
    {
        $token = $this->getRoleTokenAuth('customer');

        $uri = Config::get('app.url') . '/api/features/index';

        $allFeatures = Feature::all()->toArray();

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => $allFeatures,
                'message' => 'All features'
            ]);
    }

    public function test_feature_show_index_employee_role()
    {
        $token = $this->getRoleTokenAuth('employee');

        $uri = Config::get('app.url') . '/api/features/index';

        $allFeatures = Feature::all()->toArray();

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => $allFeatures,
                'message' => 'All features'
            ]);
    }

    public function test_feature_show_index_admin_role()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/features/index';

        $allFeatures = Feature::all()->toArray();

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => $allFeatures,
                'message' => 'All features'
            ]);
    }

    public function test_feature_show_index_owner_role()
    {
        $token = $this->getRoleTokenAuth('owner');

        $uri = Config::get('app.url') . '/api/features/index';

        $allFeatures = Feature::all()->toArray();

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => $allFeatures,
                'message' => 'All features'
            ]);
    }

    public function test_feature_show_by_id_admin_role_not_found()
    {
        $token = $this->getRoleTokenAuth('admin');

        $featureNotExists =
            Feature::max('id') + 1;

        $uri = Config::get('app.url') . '/api/features/'.$featureNotExists.'/show';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'Feature not found'
            ]);
    }

    public function test_feature_show_by_id_ok_admin_role()
    {
        $token = $this->getRoleTokenAuth('admin');

        $featureExists =
            Feature::inRandomOrder()->get()->first();

        $uri = Config::get('app.url') . '/api/features/'.$featureExists->id.'/show';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => [],
                'message' => 'Feature by id ' . $featureExists->id
            ]);
    }
}
