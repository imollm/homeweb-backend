<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CategoryUpdateTests extends TestCase
{
    public function test_update_category_customer_role_not_authorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $randCategory = Category::inRandomOrder()->first();

        $uri = Config::get('app.url') . '/api/categories/update';

        $payload = [
            'id' => $randCategory->id,
            'name' => Str::random(10),
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)->dump()
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User',
            ]);
    }

    public function test_update_category_owner_role_not_authorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $randCategory = Category::inRandomOrder()->first();

        $uri = Config::get('app.url') . '/api/categories/update';

        $payload = [
            'id' => $randCategory->id,
            'name' => Str::random(10),
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User',
            ]);
    }

    public function test_update_category_invalid_post_data_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $randCategory = Category::inRandomOrder()->first();

        $uri = Config::get('app.url') . '/api/categories/update';

        $payload = [
            'id' => $randCategory->id,
            'name' => null,
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_update_category_ok_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $randCategory = Category::inRandomOrder()->first();

        $uri = Config::get('app.url') . '/api/categories/update';

        $payload = [
            'id' => $randCategory->id,
            'name' => Str::random(10),
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)->dump()
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Category modified correctly',
            ]);
        $this->assertDatabaseHas('categories', $payload);
    }

    public function test_update_category_ok_employee_role_authorized()
    {
        $token = $this->getRoleTokenAuth('employee');

        $randCategory = Category::inRandomOrder()->first();

        $uri = Config::get('app.url') . '/api/categories/update';

        $payload = [
            'id' => $randCategory->id,
            'name' => Str::random(10),
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Category modified correctly',
            ]);
        $this->assertDatabaseHas('categories', $payload);
    }

}
