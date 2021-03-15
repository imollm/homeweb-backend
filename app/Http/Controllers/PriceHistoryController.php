<?php

namespace App\Http\Controllers;

use App\Models\PriceHistory;
use App\Services\Auth\PassportAuthService;
use App\Services\PriceHistory\PriceHistoryService;
use App\Services\Property\PropertyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PriceHistoryController extends Controller
{
    private PriceHistoryService $priceHistoryService;
    private PropertyService $propertyService;
    private PassportAuthService $passportAuthService;

    public function __construct(
        PriceHistoryService $priceHistoryService,
        PropertyService $propertyService,
        PassportAuthService $passportAuthService)
    {
        $this->priceHistoryService = $priceHistoryService;
        $this->propertyService = $propertyService;
        $this->passportAuthService = $passportAuthService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        if (Auth::user()->can('index')) {

            return response()->json([
                'success' => true,
                'data' => PriceHistory::all(),
                'message' => 'List of all price histories'
            ]);

        } else {

            return $this->unauthorizedUser();

        }
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
     * @param string $propertyId
     * @return JsonResponse
     */
    public function show(string $propertyId): JsonResponse
    {
        if (Auth::user()->can('show', PriceHistory::class)) {

            if ($this->propertyService->existsThisProperty($propertyId)) {

                $roleAuthUser = $this->passportAuthService->whatIsTheRoleOfAuthUser();

                if ($roleAuthUser === 'admin' || $roleAuthUser === 'employee') {

                    return response()->json([
                        'success' => true,
                        'data' => $this->propertyService->getPriceHistoryOfThisProperty($propertyId),
                        'message' => 'Price history of property ' . $propertyId
                    ], Response::HTTP_OK);

                } elseif (
                    $roleAuthUser === 'owner' &&
                    $this->propertyService->whichIsTheOwnerIdOfThisProperty($propertyId) === auth()->user()->id
                ) {

                    return response()->json([
                        'success' => true,
                        'data' => $this->propertyService->getPriceHistoryOfThisProperty($propertyId),
                        'message' => 'Price history of your property ' . $propertyId
                    ], Response::HTTP_OK);

                } else {

                    return response()->json([
                        'success' => false,
                        'message' => 'This property is not yours'
                    ], Response::HTTP_UNAUTHORIZED);

                }

            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'Property not found'
                ], Response::HTTP_NOT_FOUND);

            }

        } else {

            return $this->unauthorizedUser();

        }
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
