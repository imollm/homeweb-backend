<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CategoryShowTests extends TestCase
{
    public function test_show_all_categories()
    {
        $uri = Config::get('app.url') . '/api/categories/all';

        $this
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => array(),
                'message' => 'All categories'
            ]);
    }

    public function test_show_category_by_id_exists()
    {
        $randCategoryId = Category::inRandomOrder()->first()->id;

        $uri = Config::get('app.url') . '/api/categories/'.$randCategoryId.'/show';

        $this
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => array(),
                'message' => 'The category was request'
            ]);
    }

    public function test_show_category_by_id_not_exists()
    {
        $categoryNotExists = 1000; // Category not exists

        $uri = Config::get('app.url') . '/api/categories/'.$categoryNotExists.'/show';

        $this
            ->getJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'Category not found'
            ]);
    }

}
