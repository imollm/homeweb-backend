<?php


namespace App\Services\File;


use App\Models\Category;
use App\Models\Property;
use Illuminate\Http\Request;
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
            'image' => 'nullable|mimes:jpeg,png|max:1014'
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

        if ($this->isValidFile($request) === false) return false;

        $image = $request->file('image');
        $propertyRef = $request->input('reference');

        $imageName = $this->getImageName($image);

        return Storage::disk('properties')->put($imageName, File::get($image))
            ? $this->property->whereReference($propertyRef)->update(['image' => $imageName])
            : false;
    }

    /**
     * @param Request $request
     * @return bool
     * @throws ValidationException
     */
    public function storeCategoryImage(Request $request): bool
    {
        $this->validatePostFile($request, 'name');

        if ($this->isValidFile($request) === false) return false;

        $image = $request->file('image');
        $categoryName = $request->input('name');

        $imageName = $this->getImageName($image);

        return Storage::disk('categories')->put($imageName, File::get($image))
            ? $this->category->whereName($categoryName)->update(['image' => $imageName])
            : false;
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function isValidFile(Request $request): bool
    {
        return $request->file('image')->isValid();
    }

    private function getImageName(UploadedFile $image): string
    {
        return
            str_replace(' ', '_',
                Str::random(10).'_'.
                $image->getClientOriginalName().'.'.
                $image->getClientOriginalExtension());
    }
}
