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
     * @OA\Get(
     *     path="/rangePrice/index",
     *     summary="Get all range of prices",
     *     tags={"Prices Range"},
     *     @OA\Response(
     *         response=200,
     *         description="Get all range prices.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="All range of prices"),
     *         ),
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => RangePrice::all(['id','value'])->toArray(),
            'message' => 'All range of prices'
        ], Response::HTTP_OK);
    }
}
