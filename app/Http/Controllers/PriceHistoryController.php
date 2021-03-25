<?php

namespace App\Http\Controllers;

use App\Models\PriceHistory;
use App\Services\Auth\PassportAuthService;
use App\Services\PriceHistory\PriceHistoryService;
use App\Services\Property\PropertyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PriceHistoryController
 * @package App\Http\Controllers
 */
class PriceHistoryController extends Controller
{
    /**
     * @var PriceHistoryService
     */
    private PriceHistoryService $priceHistoryService;
    /**
     * @var PropertyService
     */
    private PropertyService $propertyService;
    /**
     * @var PassportAuthService
     */
    private PassportAuthService $passportAuthService;

    /**
     * PriceHistoryController constructor.
     * @param PriceHistoryService $priceHistoryService
     * @param PropertyService $propertyService
     * @param PassportAuthService $passportAuthService
     */
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
        if (Auth::user()->can('index', PriceHistory::class)) {

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
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        if (Auth::user()->can('store', PriceHistory::class)) {

            $this->priceHistoryService->validatePostData($request);

            if ((Auth::user()->role->name === 'admin' || Auth::user()->role->name === 'employee') ||
                $this->priceHistoryService->areYouAllowedToStoreAPriceChangeOfThisProperty($request)
            ) {

                if ($this->priceHistoryService->hasThisPropertyTheSamePriceChange($request)) {

                    if ($this->priceHistoryService->startTimestampGivenIsGreaterThanLast($request)) {

                        if ($this->priceHistoryService->create($request)) {

                            return response()->json([
                                'success' => true,
                                'message' => 'Price change created'
                            ], Response::HTTP_CREATED);

                        } else {

                            return response()->json([
                                'success' => false,
                                'message' => 'Error when store price change'
                            ], Response::HTTP_INTERNAL_SERVER_ERROR);

                        }

                    } else {

                        return response()->json([
                            'success' => false,
                            'message' => 'Start timestamp given is lower than last price change start timestamp'
                        ], Response::HTTP_CONFLICT);

                    }

                } else {

                    return response()->json([
                        'success' => false,
                        'message' => 'This property has identical price change'
                    ], Response::HTTP_CONFLICT);

                }

            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'The property is not yours'
                ], Response::HTTP_UNAUTHORIZED);

            }

        } else {

            return $this->unauthorizedUser();

        }
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

                $roleAuthUser = Auth::user()->role->name;

                if ($roleAuthUser === 'admin' || $roleAuthUser === 'employee') {

                    return response()->json([
                        'success' => true,
                        'data' => $this->propertyService->getPriceHistoryOfThisProperty($propertyId),
                        'message' => 'Price history of property ' . $propertyId
                    ], Response::HTTP_OK);

                } elseif (
                    $roleAuthUser === 'owner' &&
                    $this->propertyService->whichIsTheOwnerIdOfThisProperty($propertyId) == Auth::user()->id
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

}
