<?php

namespace App\Http\Controllers;

use App\Services\File\FileService;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

/**
 * Class FileController
 * @package App\Http\Controllers
 */
class FileController extends Controller
{
    /**
     * @var FileService
     */
    private FileService $fileService;

    /**
     * FileController constructor.
     * @param FileService $fileService
     */
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * @param string $id
     * @return JsonResponse
     * @throws FileNotFoundException
     */
    public function categories(string $id): JsonResponse
    {
        if (empty($file = $this->fileService->getCategoryImage($id))) abort(ResponseStatus::HTTP_NOT_FOUND);

        return response()->json([
            'success' => true,
            'data' => base64_encode($file['image']),
            'message' => 'Image'
        ], ResponseStatus::HTTP_OK);
    }

    /**
     * @param string $id
     * @return JsonResponse
     * @throws FileNotFoundException
     */
    public function properties(string $id): JsonResponse
    {
        if (empty($file = $this->fileService->getPropertyImage($id))) abort(ResponseStatus::HTTP_NOT_FOUND);

        return response()->json([
            'success' => true,
            'data' => base64_encode($file['image']),
            'message' => 'Image'
        ], ResponseStatus::HTTP_OK);
    }
}
