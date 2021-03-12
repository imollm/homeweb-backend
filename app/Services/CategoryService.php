<?php


namespace App\Services;


use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class CategoryService
 * @package App\Services
 */
class CategoryService implements CategoryServiceI
{
    /**
     * @param Request $request
     * @throws ValidationException
     */
    public function validatePostCategoryData(Request $request)
    {
        Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ])->validate();
    }

    /**
     * @param string $categoryName
     * @return bool
     */
    public function categoryExists(string $categoryName): bool
    {
        return Category::where('name', '=', strtolower($categoryName))
                ->get()
                ->count() > 0;
    }

    /**
     * @param Category $category
     * @return bool
     */
    public function hasThisCategoryProperties(Category $category): bool
    {
        $categoryId = $category->id;

        $result = DB::table('categories')
                    ->select('categories.id')
                    ->leftJoin('properties', 'categories.id', '=', 'properties.category_id')
                    ->whereNull('properties.category_id')
                    ->where('categories.id', '=', $categoryId)
                    ->get();

        // If result has value, it says that the category have not properties related
        return $result->count() === 0;
    }

    /**
     * @param Category $category
     * @return bool
     */
    public function deleteCategory(Category $category): bool
    {
        return $category->forceDelete() ? true : false;
    }
}
