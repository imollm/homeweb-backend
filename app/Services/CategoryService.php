<?php


namespace App\Services;


use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryService implements CategoryServiceI
{
    public function validatePostCategoryData(Request $request)
    {
        Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ])->validate();
    }

    public function categoryExists(string $categoryName): bool
    {
        return Category::where('name', '=', strtolower($categoryName))
                ->get()
                ->count() > 0;
    }

    public function hasThisCategoryProperties(Category $category): bool
    {
        $categoryId = $category->id;

        $result = DB::table('categories')
                    ->select('categories.id')
                    ->leftJoin('properties', 'categories.id', '=', 'properties.category_id')
                    ->whereNull('properties.category_id')
                    ->where('categories.id', '=', $categoryId)
                    ->first()
                    ->id;

        // If result has value, it says that the category have not properties related

        return $result ? false : true;
    }

    public function deleteCategory(Category $category): bool
    {
        return $category->forceDelete() ? true : false;
    }
}
