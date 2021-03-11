<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class CategoryController
 * @package App\Http\Controllers
 */
class CategoryController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
        ]);

        $categoryName = strtolower($request->input('name'));

        $categoryExists = Category::where('name', '=', $categoryName)->first();

        if ($categoryExists) {
            return response()->json([
                'success' => false,
                'message' => 'Category exists',
            ]);
        }

        if (Auth::user()->can('create', Category::class)) {

            $category = new Category();
            $category->name = $categoryName;

            if ($category->save()) {
                return response()->json([
                    'success' => true,
                    'data' => $category,
                    'message' => 'Category was added correctly',
                    'exists' => $categoryExists
                ]);
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
    public function all(): JsonResponse
    {
        $categories = Category::all();

        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'All categories'
        ]);
    }
}
