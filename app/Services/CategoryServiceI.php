<?php


namespace App\Services;


use App\Models\Category;
use Illuminate\Http\Request;

interface CategoryServiceI
{
    public function validatePostCategoryData(Request $request);
    public function categoryExists(string $name): Category | null;
    public function hasThisCategoryProperties(Category $category): bool;
    public function delete(Category $category): bool;
}
