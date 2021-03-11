<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CategoryDeleteTests extends TestCase
{
    public function test_delete_category_admin_role_authorized_category_not_have_relations_with_properties()
    {
        $token = $this->getRoleTokenAuth('admin');

        $result = DB::table('categories')
            ->select('categories.id')
            ->leftJoin('properties', 'categories.id', '=', 'properties.category_id')
            ->whereNull('properties.category_id')
            ->first()
            ->id;

        $randomCategoryIdWithoutProperties = $result;

        $uri = Config::get('app.url') . '/api/categories/'.$randomCategoryIdWithoutProperties.'/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)->dump()
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Category deleted successfully',
            ]);
    }

    public function test_delete_category_admin_role_authorized_category_have_relations_with_properties()
    {

    }
}
