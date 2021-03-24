<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Services\Sale\SaleService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SaleController
 * @package App\Http\Controllers
 */
class SaleController extends Controller
{
    /**
     * @var SaleService
     */
    private SaleService $saleService;

    /**
     * SaleController constructor.
     * @param SaleService $saleService
     */
    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        if (Auth::user()->can('store', Sale::class)) {

            $this->saleService->validatePostData($request);

            if ($this->saleService->allDataExists($request)) {

                if ($this->saleService->isToSellThisProperty($request)) {

                    if ($this->saleService->create($request)) {

                        return response()->json([
                            'success' => true,
                            'message' => 'Sale created'
                        ], Response::HTTP_CREATED);

                    } else {

                        return response()->json([
                            'success' => false,
                            'message' => 'Error while save sale'
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);

                    }

                } else {

                    return response()->json([
                        'success' => false,
                        'message' => 'This property was sold'
                    ], Response::HTTP_CONFLICT);

                }

            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'At least one actor is not available'
                ], Response::HTTP_NOT_FOUND);

            }

        } else {

            return $this->unauthorizedUser();

        }
    }

    /**
     * Display the specified resource.
     *
     * @param Sale $sale
     * @return JsonResponse
     */
    public function show(Sale $sale): JsonResponse
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Sale $sale
     * @return JsonResponse
     */
    public function update(Request $request, Sale $sale): JsonResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Sale $sale
     * @return JsonResponse
     */
    public function destroy(Sale $sale): JsonResponse
    {
        //
    }
}
