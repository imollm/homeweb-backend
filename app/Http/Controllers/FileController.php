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
     * @return Response
     */
    public function categories(string $id): Response
    {
        if ($category = $this->categoryService->getCategoryById($id)) {
            try {
                $image = Storage::disk('categories')->get($category['image']);
            }
            catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
                return new Response(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
            }
        }
        return new Response($image, Response::HTTP_OK, ['Content-type' => 'image/jpg']);
    }

    /**
     * @param string $id
     * @return Response
     */
    public function properties(string $id): Response
    {
        if ($property = $this->propertyService->getPropertyById($id)) {
            try {
                $image = Storage::disk('properties')->get($property->image);
            }
            catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
                return new Response(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
            }
        }
        return new Response($image, Response::HTTP_OK, ['Content-type' => 'image/jpg']);
    }
}
