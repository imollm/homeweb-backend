<?php


namespace App\Services\Category;


use App\Models\Category;
use Illuminate\Http\Request;

interface ICategoryService
{
    public function validatePostCategoryData(Request $request);
    public function categoryExists(string $name): bool;
    public function hasThisCategoryProperties(Category $category): bool;
    public function delete(Category $category): bool;
}
