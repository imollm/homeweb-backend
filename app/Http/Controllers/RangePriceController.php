<?php

namespace App\Http\Controllers;

use App\Models\RangePrice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RangePriceController
 * @package App\Http\Controllers
 */
class RangePriceController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => RangePrice::all()->toArray(),
            'message' => 'All range of prices'
        ], Response::HTTP_OK);
    }
}
