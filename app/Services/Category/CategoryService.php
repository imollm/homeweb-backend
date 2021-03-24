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
     * @var Category
     */
    private Category $category;

    /**
     * CategoryService constructor.
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

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
        return !is_null($this->category->find($id));
    }

    /**
     * @param string $name
     * @return bool
     */
    public function categoryExistsByName(string $name): bool
    {
        return !is_null($this->category->where('name', $name)->get()->first());
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
        return $this->category->find($id)->delete();
    }

    /**
     * @return array
     */
    public function getAllCategories(): array
    {
        return $this->category->all()->toArray();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function create(string $name): bool
    {
        $newCategory = $this->category->create(['name' => $name]);

        return !is_null($newCategory);
    }

    /**
     * @param string $id
     * @return array
     */
    public function getCategoryById(string $id): array
    {
        $category = $this->category->find($id);

        return !is_null($category) ? $category->toArray() : [];
    }
}
