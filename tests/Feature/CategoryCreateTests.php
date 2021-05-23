<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CategoryCreateTests extends TestCase
{
    public function test_create_category_customer_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $uri = Config::get('app.url') . '/api/categories/create';

        $payload = [
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

    public function test_create_category_owner_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('owner');

        $uri = Config::get('app.url') . '/api/categories/create';

        $payload = [
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

    public function test_create_category_admin_role_authorized_invalid_post_data()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/categories/create';

        $payload = [
            'name' => 1890, // Invalid data type
            'image' => 'image.jpg'
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_create_category_admin_role_authorized_category_already_exists()
    {
        $token = $this->getRoleTokenAuth('admin');

        $categoryNameAlreadyExists = Category::inRandomOrder()->first()->name;

        $uri = Config::get('app.url') . '/api/categories/create';

        $payload = [
            'name' => $categoryNameAlreadyExists,
            'image' => 'image.jpg'
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_create_category_employee_role_authorized()
    {
        $token = $this->getRoleTokenAuth('employee');

        $uri = Config::get('app.url') . '/api/categories/create';

        $payload = [
            'name' => Str::random(10),
            'image' => 'image.jpg'
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_create_category_ok_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/categories/create';

        $payload = [
            'name' => Str::random(10),
            'image' => 'image.jpg'
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

}
