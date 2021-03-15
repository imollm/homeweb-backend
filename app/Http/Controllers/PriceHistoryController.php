<?php

namespace App\Http\Controllers;

use App\Models\PriceHistory;
use App\Services\PriceHistory\PriceHistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PriceHistoryController extends Controller
{
    private PriceHistoryService $priceHistoryService;

    public function __construct(PriceHistoryService $priceHistoryService)
    {
        $this->priceHistoryService = $priceHistoryService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param PriceHistory $priceHistory
     * @return JsonResponse
     */
    public function show(PriceHistory $priceHistory): JsonResponse
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param PriceHistory $priceHistory
     * @return JsonResponse
     */
    public function update(Request $request, PriceHistory $priceHistory): JsonResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param PriceHistory $priceHistory
     * @return JsonResponse
     */
    public function destroy(PriceHistory $priceHistory): JsonResponse
    {
        //
    }
}
