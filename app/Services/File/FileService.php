<?php


namespace App\Services\File;


use App\Models\Category;
use App\Models\Property;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Class FileService
 * @package App\Services\File
 */
class FileService implements IFileService
{
    /**
     * @var Property
     */
    private Property $property;

    /**
     *
     */
    private Category $category;

    /**
     * FileService constructor.
     * @param Property $property
     * @param Category $category
     */
    public function __construct(Property $property, Category $category)
    {
        $this->property = $property;
        $this->category = $category;
    }

    /**
     * @param Request $request
     * @param string $categoryOrProperty
     * @throws ValidationException
     */
    private function validatePostFile(Request $request, string $categoryOrProperty)
    {
        Validator::make($request->all(), [
            $categoryOrProperty => 'required',
            'image' => 'nullable|mimes:jpeg,png,jpg|image'
        ])->validate();
    }

    /**
     * @param Request $request
     * @return bool
     * @throws ValidationException
     */
    public function storePropertyImage(Request $request): bool
    {
        $this->validatePostFile($request, 'reference');

        if (!$request->file('image')->isValid()) return false;

        $propertyRef = $request->input('reference');

        $imageName = Storage::disk('properties')->put('', $request->file('image'));

        return $imageName ? $this->property->whereReference($propertyRef)->update(['image' => $imageName]) : false;
    }

    /**
     * @param Request $request
     * @return bool
     * @throws ValidationException
     */
    public function storeCategoryImage(Request $request): bool
    {
        $this->validatePostFile($request, 'name');

        if (!$request->file('image')->isValid()) return false;

        $categoryName = $request->input('name');

        $imageName = Storage::disk('categories')->put('', $request->file('image'));

        return $imageName ? $this->category->whereName($categoryName)->update(['image' => $imageName]) : false;
    }


    /**
     * @param string $fileName
     * @return array
     * @throws FileNotFoundException
     */
    public function getPropertyImage(string $fileName): array
    {
        $file = [];
        if ($this->property->whereImage($fileName)) {

            $file['image'] = Storage::disk('properties')->get($fileName);
            $file['type'] = Storage::disk('properties')->mimeType($fileName);
        }
        return $file;
    }

    /**
     * @param string $fileName
     * @return array
     * @throws FileNotFoundException
     */
    public function getCategoryImage(string $fileName): array
    {
        $file = [];
        if ($this->category->whereImage($fileName)) {

            $file['image'] = Storage::disk('categories')->get($fileName);
            $file['type'] = Storage::disk('categories')->mimeType($fileName);
        }
        return $file;
    }
}
