<?php


namespace App\Services\File;


use Illuminate\Http\Request;

/**
 * Interface IFileService
 * @package App\Services\File
 */
interface IFileService
{
    /**
     * @param Request $request
     * @return bool
     */
    public function storePropertyImage(Request $request): bool;

    /**
     * @param Request $request
     * @return bool
     */
    public function storeCategoryImage(Request $request): bool;
}
