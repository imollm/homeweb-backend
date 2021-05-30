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
use Psy\Util\Json;
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
     * PriceHistoryController constructor.
     * @param PriceHistoryService $priceHistoryService
     * @param PropertyService $propertyService
     */
    public function __construct(
        PriceHistoryService $priceHistoryService,
        PropertyService $propertyService)
    {
        $this->priceHistoryService = $priceHistoryService;
        $this->propertyService = $propertyService;
    }

    /**
     * @OA\Get(
     *     path="/priceHistory/index",
     *     summary="Get all price changes",
     *     tags={"Price History"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="List of all price histories.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="List of all price histories"),
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
    public function index(): JsonResponse
    {
        if (Auth::user()->can('index', PriceHistory::class)) {

            return response()->json([
                'success' => true,
                'data' => $this->priceHistoryService->getAllChanges(),
                'message' => 'List of all price histories'
            ], Response::HTTP_OK);

        } else {

            return $this->unauthorizedUser();

        }
    }

    /**
     * @OA\Post(
     *     path="/priceHistory/create",
     *     summary="Store new price change",
     *     tags={"Price History"},
     *     security={{ "apiAuth": {} }},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Country data",
     *          @OA\JsonContent(
     *             @OA\Property(property="property_id", description="ID of property", type="integer", example=1),
     *             @OA\Property(property="start_date", description="Start date", type="string", example="2021-02-01 12:00:45"),
     *             @OA\Property(property="amount", description="New price", type="integer", example=1000000),
     *             @OA\Property(property="end_date", description="Start date", type="string", example=""),
     *          ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Price change created.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="Price change created"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error when store price change.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Error when store price change"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Incorrect start date, or Property have same price.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Start timestamp given is lower or equal than last price change start timestamp / This property has identical price change"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid put data."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Check if is yours, or Unauthorized user.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="The property is not yours / Unauthorized User"),
     *         ),
     *     ),
     * )
     * @throws ValidationException
     */
    public function create(Request $request): JsonResponse
    {
        if (Auth::user()->can('create', PriceHistory::class)) {

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
                            'message' => 'Start timestamp given is lower or equal than last price change start timestamp'
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
     * @OA\Get(
     *     path="/priceHistory/{propertyId}/show",
     *     summary="Get price history by property id",
     *     tags={"Price History"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Parameter (
     *         name="propertyId",
     *         in="path",
     *         required=true,
     *         description="ID of property"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Price history of this property.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="Price history of property"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Property not found.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Property not found"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="This property is not yours or Unauthorized user.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="This property is not yours / Unauthorized User"),
     *         ),
     *     )
     * )
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
                    $this->propertyService->whichIsTheOwnerIdOfThisProperty($propertyId) == Auth::id()
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
     * @OA\Get(
     *     path="/priceHistory/propertiesOfOwner",
     *     summary="Get price changes of properties owned by owner",
     *     tags={"Price History"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Price changes of property owned by owner.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="Price changes of properties owned by owner with id X"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized user.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="TUnauthorized User"),
     *         ),
     *     )
     * )
     */
    public function getPriceChangeOfAuthOwner(): JsonResponse
    {
        if (Auth::user()->role->name === 'owner') {
            return response()->json([
                'success' => true,
                'data' => $this->priceHistoryService->getPriceChangesOfPropertiesOwnedByAuthOwner(),
                'message' => 'Price changes of properties owned by owner with id ' . Auth::id()
            ], Response::HTTP_OK);
        } else {
            return $this->unauthorizedUser();
        }
    }

}
