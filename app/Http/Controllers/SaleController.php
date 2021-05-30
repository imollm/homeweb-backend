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
     * @OA\Get(
     *     path="/sales/{limit}/index",
     *     summary="Get limited sales",
     *     tags={"Sales"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="All categories.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="All sales of user X with role Y"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No sales in system.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="No sales in system"),
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
    public function index(int $limit): JsonResponse
    {
        if (Auth::user()->can('index', Sale::class)) {

            $authRole = Auth::user()->role->name;
            $authUserId = Auth::user()->id;


            $sales = match ($authRole) {
                'admin' => $this->saleService->getLastSales($limit),
                'employee' => $this->saleService->getSalesByEmployeeId($authUserId),
                'owner' => $this->saleService->getSalesByOwnerId($authUserId),
                'customer' => $this->saleService->getSalesByCustomerId($authUserId),
                default => [],
            };

            if (count($sales) > 0) {

                return response()->json([
                    'success' => true,
                    'data' => $sales,
                    'message' => 'All sales of user ' . $authUserId . ' with role ' . $authRole
                ], Response::HTTP_OK);

            } else {

                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'No sales in system'
                ], Response::HTTP_NOT_FOUND);

            }

        } else {

            return $this->unauthorizedUser();

        }
    }

    /**
     * @OA\Post (
     *     path="/sales/create",
     *     summary="Store new sale",
     *     tags={"Sales"},
     *     security={{ "apiAuth": {} }},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Sale data",
     *          @OA\JsonContent(
     *               @OA\Property(property="property_id", description="Property ID", type="integer", example=1),
     *               @OA\Property(property="buyer_id", description="Customer ID", type="integer", example=1),
     *               @OA\Property(property="seller_id", description="Employee ID", type="integer", example=1),
     *               @OA\Property(property="date", description="Date of sale", type="string", example="2021-05-03"),
     *               @OA\Property(property="amount", description="Property final price of sale", type="integer", example=1000000),
     *          ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Sale created.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="Sale created"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="This property was sold.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="This property was sold"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error while save sale.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Error while save sale"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="At least one actor not found",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="At least one actor is not available"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid put data.",
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
    public function create(Request $request): JsonResponse
    {
        if (Auth::user()->can('create', Sale::class)) {

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
     * @OA\Get(
     *     path="/sales/{hashId}/showByHashId",
     *     summary="Get sale by hash id",
     *     tags={"Sales"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Parameter (
     *         name="hashId",
     *         in="path",
     *         required=true,
     *         description="Hash ID of sale"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sale by hash id X.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="Sale by hash id X"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Sale not found.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Any sale with this params"),
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
    public function showByHashId(string $hashId): JsonResponse
    {
        if (Auth::user()->can('showByHashId', Sale::class)) {

            $authRole = Auth::user()->role->name;
            $authUserId = Auth::user()->id;

            if ($sale = $this->saleService->getSaleByHashId($hashId, $authRole, $authUserId)) {

                    return response()->json([
                        'success' => true,
                        'data' => $sale,
                        'message' => 'Sale by hash id ' . $hashId
                    ], Response::HTTP_OK);

            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'Any sale with this params'
                ], Response::HTTP_NOT_FOUND);

            }

        } else {

            return $this->unauthorizedUser();

        }
    }

    /**
     * @OA\Get(
     *     path="/sales/actualYear",
     *     summary="Get sales of actual year",
     *     tags={"Sales"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Sales of 2021",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="Sales of 2021"),
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
    public function getSalesOfActualYear(): JsonResponse
    {
        if (Auth::user()->can('getSalesOfActualYear', Sale::class)) {

            $actualYear = date('Y');

            $sales = $this->saleService->getSalesOfActualYear($actualYear);

            return response()->json([
                'success' => true,
                'data' => $sales,
                'message' => 'Sales of ' . $actualYear
            ], Response::HTTP_OK);

        } else {

            return $this->unauthorizedUser();

        }
    }

    /**
     * @OA\Get(
     *     path="/sales/salesBy",
     *     summary="Get sales by categories, countries, cities and sellers",
     *     tags={"Sales"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Sales by categories, countries, cities and sellers",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="Sales by categories, countries, cities and sellers"),
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
    public function salesBy(): JsonResponse
    {
        if (Auth::user()->can('salesBy', Sale::class)) {
            $sales = [
                'byCategories' => $this->saleService->getSalesByCategories(),
                'byCountries' => $this->saleService->getSalesByCountries(),
                'byCities' => $this->saleService->getSalesByCities(),
                'bySellers' => $this->saleService->getSalesBySellers()
            ];

            return response()->json([
                'success' => true,
                'data' => $sales,
                'message' => 'Sales by categories, countries, cities and sellers'
            ], Response::HTTP_OK);
        } else {
            return $this->unauthorizedUser();
        }
    }

    /**
     * @OA\Put(
     *     path="/sales/update",
     *     summary="Update sale",
     *     tags={"Sales"},
     *     security={{ "apiAuth": {} }},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Sale data",
     *          @OA\JsonContent(
     *               @OA\Property(property="property_id", description="Property ID", type="integer", example=1),
     *               @OA\Property(property="buyer_id", description="Customer ID", type="integer", example=1),
     *               @OA\Property(property="seller_id", description="Employee ID", type="integer", example=1),
     *               @OA\Property(property="date", description="Date of sale", type="string", example="2021-05-03"),
     *               @OA\Property(property="amount", description="Property final price of sale", type="integer", example=1000000),
     *          ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sale updated successfully.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="Sale updated successfully"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error while update sale.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Error while update sale"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Sale not exists",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Sale not exists"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Your are not related or Unauthorized user.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Your are not related / Unauthorized User"),
     *         ),
     *     )
     * )
     */
    public function update(Request $request): JsonResponse
    {
        if (Auth::user()->can('update', Sale::class)) {
            $hashId = $request->input('hash_id');

            if ($this->saleService->exitsThisSale($hashId)) {
                if (Auth::user()->role->name === 'employee' && !$this->saleService->isThisSaleOfThisSeller($hashId, Auth::id())){
                    return response()->json([
                        'success' => false,
                        'message' => 'Your are not related'
                    ], Response::HTTP_UNAUTHORIZED);
                }
                if ($this->saleService->update($request)) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Sale updated successfully'
                    ], Response::HTTP_OK);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error while update sale'
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Sale not exists'
                ], Response::HTTP_NOT_FOUND);
            }
        } else {
            return $this->unauthorizedUser();
        }
    }

    /**
     * @OA\Get(
     *     path="/sales/mySales",
     *     summary="Get sales of seller (user with employee role)",
     *     tags={"Sales"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Sales of employee with id X",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="Sales of employee with id X"),
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
    public function getSalesBySeller(): JsonResponse
    {
        if (Auth::user()->can('getSalesBySeller', Sale::class)) {
            return response()->json([
                'success' => true,
                'data' => $this->saleService->getSalesOfAuthSeller(),
                'message' => 'Sale of employee with id ' . Auth::id()
            ], Response::HTTP_OK);
        } else {
            return $this->unauthorizedUser();
        }
    }

    /**
     * @OA\Get(
     *     path="/sales/myPurchases",
     *     summary="Get sales of buyer (user with customer role)",
     *     tags={"Sales"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Purchases of customer with id X",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="Purchases of customer with id X"),
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
    public function getPurchasesByCustomer(): JsonResponse
    {
        if (Auth::user()->role->name === 'customer') {
            return response()->json([
                'success' => true,
                'data' => $this->saleService->getPurchasesOfAuthBuyer(),
                'message' => 'Purchases of customer with id ' . Auth::id()
            ]);
        } else {
            return $this->unauthorizedUser();
        }
    }
}
