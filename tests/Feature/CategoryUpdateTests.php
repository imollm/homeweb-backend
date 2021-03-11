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
    public function test_update_category_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $randomCategoryId = Category::inRandomOrder()->first()->id;

        $uri = Config::get('app.url') . '/api/categories/'.$randomCategoryId.'/update';

        $payload = [
            'name' => Str::random(10),
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Category modified correctly',
            ]);
        $this->assertDatabaseHas('categories', $payload);
    }

    public function test_update_category_employee_role_authorized()
    {
        $token = $this->getRoleTokenAuth('employee');

        $randomCategoryId = Category::inRandomOrder()->first()->id;

        $uri = Config::get('app.url') . '/api/categories/'.$randomCategoryId.'/update';

        $payload = [
            'name' => Str::random(10),
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Category modified correctly',
            ]);
        $this->assertDatabaseHas('categories', $payload);
    }

    public function test_update_category_customer_role_not_authorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $randomCategoryId = Category::inRandomOrder()->first()->id;

        $uri = Config::get('app.url') . '/api/categories/'.$randomCategoryId.'/update';

        $payload = [
            'name' => Str::random(10),
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User',
            ]);
    }

    public function test_update_category_owner_role_not_authorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $randomCategoryId = Category::inRandomOrder()->first()->id;

        $uri = Config::get('app.url') . '/api/categories/'.$randomCategoryId.'/update';

        $payload = [
            'name' => Str::random(10),
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User',
            ]);
    }
}
