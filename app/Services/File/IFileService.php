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
     */
    public function validatePostFile(Request $request);

    /**
     * @param Request $request
     * @param string $disk
     * @return string|bool
     */
    public function storeImage(Request $request, string $disk): string | bool;
}
