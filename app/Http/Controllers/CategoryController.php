<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\Category\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CategoryController
 * @package App\Http\Controllers
 */
class CategoryController extends Controller
{
    /**
     * @var CategoryService
     */
    private CategoryService $categoryService;

    /**
     * CategoryController constructor.
     *
     * @param CategoryService $categoryService
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Create a new category
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function create(Request $request): JsonResponse
    {
        if (Auth::user()->can('create', Category::class)) {

            $categoryName = $request->input('name');

            $this->categoryService->validatePostCategoryData($request);

            if ($this->categoryService->categoryExistsByName($categoryName)) {

                return response()->json([
                    'success' => false,
                    'message' => 'Category exists',
                ], Response::HTTP_CONFLICT);

            }

            else {

                if ($this->categoryService->create($categoryName)) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Category added correctly',
                    ], Response::HTTP_CREATED);
                }

                else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Category not added',
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
                }

            }

        } else {
            return $this->unauthorizedUser();
        }
    }

    /**
     * Return all categories
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->categoryService->getAllCategories(),
            'message' => 'All categories'
        ], Response::HTTP_OK);
    }

    /**
     * Show a category by id
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        if ($this->categoryService->categoryExistsById($id)) {
            return response()->json([
                'success' => true,
                'data' => $this->categoryService->getCategoryById($id),
                'message' => 'The category was request'
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], Response::HTTP_NOT_FOUND);
        }

    }

    /**
     * Update a category
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, string $id): JsonResponse
    {
        if (Auth::user()->can('update', Category::class)) {

            $this->categoryService->validatePostCategoryData($request);

            if (Category::updateOrCreate($request->all())) {
                return response()->json([
                    'success' => true,
                    'message' => 'Category modified correctly',
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not modified',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

        } else {
            return $this->unauthorizedUser();
        }
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function delete(string $id): JsonResponse
    {
        if (Auth::user()->can('delete', Category::class)) {

            if ($this->categoryService->categoryExistsById($id)) {

                if (!$this->categoryService->hasThisCategoryProperties($id)) {

                    if ($this->categoryService->delete($id)) {

                        return response()->json([
                            'success' => true,
                            'message' => 'Category deleted successfully',
                        ], Response::HTTP_OK);

                    }

                    else {

                        return response()->json([
                            'success' => false,
                            'message' => 'Category not deleted',
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);

                    }

                } else {

                    return response()->json([
                        'success' => false,
                        'message' => 'Category has properties',
                    ], Response::HTTP_CONFLICT);

                }

            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'This category not found',
                ], Response::HTTP_NOT_FOUND);

            }

        } else {
            return $this->unauthorizedUser();
        }
    }
}
