<?php


namespace App\Services\Category;


use App\Models\Category;
use App\Models\Property;
use App\Services\File\FileService;
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
     * @var FileService
     */
    private FileService $fileService;

    /**
     * @var Property
     */
    private Property $property;

    /**
     * CategoryService constructor.
     * @param Category $category
     * @param FileService $fileService
     * @param Property $property
     */
    public function __construct(Category $category, FileService $fileService, Property $property)
    {
        $this->category = $category;
        $this->fileService = $fileService;
        $this->property = $property;
    }

    /**
     * @param Request $request
     * @throws ValidationException
     */
    public function validatePostCategoryData(Request $request)
    {
        Validator::make($request->all(), [
            'name' => 'required|unique:categories|max:255',
        ])->validate();
    }

    /**
     * @param Request $request
     * @throws ValidationException
     */
    public function validatePutCategoryData(Request $request)
    {
        Validator::make($request->all(), [
            'id' => 'required|numeric',
            'name' => 'required|unique:categories|max:255',
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
        return $this->category->with('properties')
                    ->with('propertiesCount')
                    ->get()
                    ->toArray();
    }

    /**
     * @param Request $request
     * @return bool
     * @throws ValidationException
     */
    public function create(Request $request): bool
    {
        $newCategory = $this->category->create(['name' => $request->input('name')]);

        return !is_null($newCategory) && $this->fileService->storeCategoryImage($request);
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request): bool
    {
        $categoryId = $request->input('id');
        $categoryName = $request->input('name');

        $updated = $this->category->updateOrCreate(['id' => $categoryId], ['name' => $categoryName]);

        if ($request->hasFile('image') && !is_null($request->file('image'))) $this->fileService->storeCategoryImage($request);

        return !is_null($updated);
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

    /**
     * @param string $name
     * @return array
     */
    public function getPropertiesByCategoryName(string $name): array
    {
        return  $this->category
                    ->whereName($name)
                    ->get()
                    ->first()
                    ->properties()
                    ->with('city')
                    ->get()
                    ->toArray();
    }

    /**
     * @param string $id
     * @return array
     */
    public function getPropertiesByCategoryId(string $id): array
    {
        $category = $this->category->find($id);

        $properties = $category
                        ->find($id)
                        ->properties()
                        ->with('city')
                        ->get()
                        ->toArray();

        $category['properties'] = $properties;
        $result['category'] = $category;

        return array($result);
    }

    /**
     * @param string $id
     * @return array
     */
    public function getPropertiesGroupByPrice(string $id): array
    {
        return $this->property
                    ->select('price', DB::raw('count(*) as count'))
                    ->whereCategoryId($id)
                    ->groupBy('price')
                    ->get()
                    ->toArray();
    }
}
