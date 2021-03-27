<?php

namespace App\Http\Controllers;

use App\Services\Category\CategoryService;
use App\Services\Property\PropertyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FileController
 * @package App\Http\Controllers
 */
class FileController extends Controller
{
    /**
     * @var PropertyService
     */
    private PropertyService $propertyService;
    /**
     * @var CategoryService
     */
    private CategoryService $categoryService;

    /**
     * FileController constructor.
     * @param PropertyService $propertyService
     * @param CategoryService $categoryService
     */
    public function __construct(PropertyService $propertyService, CategoryService $categoryService)
    {
        $this->propertyService = $propertyService;
        $this->categoryService = $categoryService;
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function categories(string $id): JsonResponse
    {
        if ($category = $this->categoryService->getCategoryById($id)) {
            try {
                $image = Storage::disk('categories')->get($category['image']);
            }
            catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
                return response()->json([
                    'success' => true,
                    'message' => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        return response()->json([
            'success' => true,
            'data' => base64_encode($image),
            'message' => 'Request image'
        ], Response::HTTP_OK, ['Content-type' => 'image/jpg']);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function properties(string $id): JsonResponse
    {
        if ($property = $this->propertyService->getPropertyById($id)) {
            try {
                $image = Storage::disk('properties')->get($property->image);
            }
            catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
                return response()->json([
                    'success' => true,
                    'message' => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        return response()->json([
            'success' => true,
            'data' => base64_encode($image),
            'message' => 'Request image'
        ], Response::HTTP_OK, ['Content-type' => 'image/jpg']);
    }
}
