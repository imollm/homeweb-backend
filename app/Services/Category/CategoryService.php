<?php


namespace App\Services\Category;


use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class CategoryService
 * @package App\Services
 */
class CategoryService implements ICategoryService
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
     * @param string $id
     * @return bool
     */
    public function categoryExistsById(string $id): bool
    {
        return !is_null(Category::find($id));
    }

    /**
     * @param string $name
     * @return bool
     */
    public function categoryExistsByName(string $name): bool
    {
        return !is_null(Category::where('name', $name)->get()->first());
    }

    /**
     * @param string $id
     * @return bool
     */
    public function hasThisCategoryProperties(string $id): bool
    {
        $result = DB::table('categories')
                    ->select('categories.id')
                    ->leftJoin('properties', 'categories.id', '=', 'properties.category_id')
                    ->whereNull('properties.category_id')
                    ->where('categories.id', '=', $id)
                    ->get();

        // If result has value, it says that the category have not properties related
        return $result->count() === 0;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool
    {
        return Category::find($id)->delete();
    }
}
