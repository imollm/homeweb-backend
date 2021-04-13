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
        if (Auth::user()->can('index', Sale::class)) {

            $authRole = Auth::user()->role->name;
            $authUserId = Auth::user()->id;

            $sales = match ($authRole) {
                'admin' => $this->saleService->getLastSales(),
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

                return response()->json([], Response::HTTP_NO_CONTENT);

            }

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
     * @param string $hashId
     * @return JsonResponse
     */
    public function showByHashId(string $hashId): JsonResponse
    {
        if (Auth::user()->can('showByHashId', Sale::class)) {

            $authRole = Auth::user()->role->name;
            $authUserId = Auth::user()->id;

            if ($sale = $this->saleService->getSaleByHashId($hashId, $authRole, $authUserId)) {

                if (count($sale) > 0) {

                    return response()->json([
                        'success' => true,
                        'data' => $sale,
                        'message' => 'Sale by hash id ' . $hashId
                    ], Response::HTTP_OK);

                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Any sale with this params'
            ], Response::HTTP_NOT_FOUND);

        } else {

            return $this->unauthorizedUser();

        }
    }

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
}
