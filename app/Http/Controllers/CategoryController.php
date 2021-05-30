<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\Category\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
     * @OA\Post(
     *     path="/categories/create",
     *     summary="Store new category",
     *     tags={"Categories"},
     *     security={{ "apiAuth": {} }},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Category data",
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema (
     *                  @OA\Property(property="name", description="Name of category", type="string", example="Foo"),
     *                  @OA\Property(property="image[]", description="Image of category", type="array", @OA\Items(type="string", format="binary")),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category added correctly.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="Category added correctly"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Category exists.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Category exists"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Category not added.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Category not added"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized user.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Unauthorized User"),
     *         ),
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        if (Auth::user()->can('store', Category::class)) {

            $categoryName = $request->input('name');

            $this->categoryService->validatePostCategoryData($request);

            if ($this->categoryService->categoryExistsByName($categoryName)) {

                return response()->json([
                    'success' => false,
                    'message' => 'Category exists',
                ], Response::HTTP_CONFLICT);

            }

            else {

                if ($this->categoryService->create($request)) {
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
     * @OA\Get(
     *     path="/categories/index",
     *     summary="Get all categories",
     *     tags={"Categories"},
     *     @OA\Response(
     *         response=200,
     *         description="All categories.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="All categories"),
     *         ),
     *     )
     * )
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
     * @OA\Get(
     *     path="/categories/{id}/show",
     *     summary="Get category by id",
     *     tags={"Categories"},
     *     @OA\Parameter (
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of category"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="The category was request.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="The category was request"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Category not found"),
     *         ),
     *     )
     * )
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
     * @OA\Post(
     *     path="/categories/update",
     *     summary="Update category",
     *     tags={"Categories"},
     *     security={{ "apiAuth": {} }},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Category data",
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema (
     *                  @OA\Property(property="name", description="Name of category", type="string", example="Foo"),
     *                  @OA\Property(property="image[]", description="Image of category", type="array", @OA\Items(type="string", format="binary")),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category modified correctly.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="Category modified correctly"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Category not modified.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Category not modified"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid put data."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized user.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Unauthorized User"),
     *         ),
     *     )
     * )
     * @throws ValidationException
     */
    public function update(Request $request): JsonResponse
    {
        if (Auth::user()->can('update', Category::class)) {

            $this->categoryService->validatePutCategoryData($request);

            if ($this->categoryService->update($request)) {
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
     * @OA\Delete(
     *     path="/categories/{id}/delete",
     *     summary="Delete category by id",
     *     tags={"Categories"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Parameter (
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of category"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category deleted successfully.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="Category deleted successfully"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Category not deleted.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Category not deleted"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Category has properties.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Category has properties"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="This category not found.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="This category not found"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized user.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Unauthorized User"),
     *         ),
     *     )
     * )
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

    /**
     * @OA\Get(
     *     path="/categories/name/{name}/properties",
     *     summary="Get all properties by category name",
     *     tags={"Categories"},
     *     @OA\Parameter (
     *         name="name",
     *         in="path",
     *         required=true,
     *         description="Name of category"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="All properties of {categoryName} category.",
     *     )
     * )
     */
    public function getPropertiesByCategoryName(string $name): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->categoryService->getPropertiesByCategoryName($name),
            'message' => 'All properties of ' . $name . ' category'
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/id/{id}/properties",
     *     summary="Get all properties by category ID",
     *     tags={"Categories"},
     *     @OA\Parameter (
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of category"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="All properties of category by id {id}.",
     *     )
     * )
     */
    public function getPropertiesByCategoryId(string $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->categoryService->getPropertiesByCategoryId($id),
            'message' => 'All properties of category by id ' . $id
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/categories/{id}/properties/groupByPrice",
     *     summary="Group properties of category by price",
     *     tags={"Categories"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Parameter (
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of category"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Properties of category {id} group by price.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="Properties of category {id} group by price"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized user.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Unauthorized User"),
     *         ),
     *     )
     * )
     */
    public function getPropertiesGroupByPrice(string $id): JsonResponse
    {
        if (Auth::user()->can('propertiesGroupByPrice', Category::class)) {
            return response()->json([
                'success' => true,
                'data' => $this->categoryService->getPropertiesGroupByPrice($id),
                'message' => 'Properties of category ' . $id . ' group by price'
            ], Response::HTTP_OK);
        } else {
            return $this->unauthorizedUser();
        }
    }
}
