<?php


namespace App\Services\File;


use Illuminate\Http\Request;
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
     * @param Request $request
     * @throws ValidationException
     */
    public function validatePostFile(Request $request)
    {
        $categoryOrProperty = $request->has('reference') ? 'reference' : 'name';

        Validator::make($request->all(), [
            $categoryOrProperty => 'required',
            'image' => 'mimes:jpeg,png|max:1014'
        ])->validate();
    }

    /**
     * @param Request $request
     * @param string $disk
     * @return string|bool
     */
    public function storeImage(Request $request, string $disk): string | bool
    {
        if ($request->has('image') && $request->file('image')->isValid()) {

            $image = $request->file('image');

            ($disk === 'categories') ?
                $categoryOrProperty = 'name' :
                $categoryOrProperty = 'reference';

            $image_path =
                str_replace(' ', '_',
                    Str::random(10).'_'.
                    $request->input($categoryOrProperty).'.'.
                    $request->image->extension());

            if (Storage::disk($disk)->put($image_path, File::get($image))) {
                return $image_path;
            }
        }
        return false;
    }
}
