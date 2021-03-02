<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
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
