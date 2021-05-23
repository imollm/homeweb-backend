<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CategoryDeleteTests extends TestCase
{
    public function test_delete_category_customer_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $randCategoryId = Category::inRandomOrder()->first()->id;

        $uri = Config::get('app.url') . '/api/categories/'.$randCategoryId.'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User',
            ]);
    }

    public function test_delete_category_category_not_found_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $categoryIdNotExists = Category::max('id') + 1;

        $uri = Config::get('app.url') . '/api/categories/'.$categoryIdNotExists.'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'This category not found',
            ]);
    }

    public function test_delete_category_have_relations_with_properties_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $randCategoryIdRelatedWithProperty =
            DB::table('categories')
                ->join('properties', 'categories.id', '=', 'properties.category_id')
                ->pluck('categories.id')
                ->first();

        $uri = Config::get('app.url') . '/api/categories/'.$randCategoryIdRelatedWithProperty.'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson([
                'success' => false,
                'message' => 'Category has properties',
            ]);
    }

    public function test_delete_category_ok_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $randomCategoryIdWithOutProperties =
            DB::table('categories')
                ->select('categories.id')
                ->leftJoin('properties', 'categories.id', '=', 'properties.category_id')
                ->whereNull('properties.category_id')
                ->first()
                ->id;

        $uri = Config::get('app.url') . '/api/categories/'.$randomCategoryIdWithOutProperties.'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Category deleted successfully',
            ]);
    }

}
