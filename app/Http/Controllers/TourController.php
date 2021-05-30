<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Services\Property\PropertyService;
use App\Services\Tour\TourService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TourController
 * @package App\Http\Controllers
 */
class TourController extends Controller
{
    /**
     * @var TourService
     */
    private TourService $tourService;
    /**
     * @var PropertyService
     */
    private PropertyService $propertyService;

    /**
     * TourController constructor.
     * @param TourService $tourService
     * @param PropertyService $propertyService
     */
    public function __construct(TourService $tourService, PropertyService $propertyService)
    {
        $this->tourService = $tourService;
        $this->propertyService = $propertyService;
    }

    /**
     * @OA\Get(
     *     path="/tours/index",
     *     summary="Get all tours",
     *     tags={"Tours"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="All tours.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="All tours"),
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
        if (Auth::user()->can('index', Tour::class)) {

            return response()->json([
                'success' => true,
                'data' => Tour::all(),
                'message' => 'All tours'
            ], Response::HTTP_OK);

        } else {

            return $this->unauthorizedUser();

        }
    }

    /**
     * @OA\Post(
     *     path="/tours/create",
     *     summary="Store new tour",
     *     tags={"Tours"},
     *     security={{ "apiAuth": {} }},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Tour data",
     *          @OA\JsonContent(
     *             @OA\Property(property="property_id", description="Property ID", type="string", example=1),
     *             @OA\Property(property="customer_id", description="Customer ID", type="string", example=2),
     *             @OA\Property(property="employee_id", description="Employee ID", type="string", example=3),
     *             @OA\Property(property="date", description="Date", type="string", example="2021-03-01"),
     *             @OA\Property(property="time", description="Hour", type="string", example="10:00:00"),
     *          ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tour created.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="Tour created"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="At least one actor is not available, choose another combination.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="At least one actor is not available, choose another combination"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Customer or employee or property not found.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Some resource not found"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error while create tour.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Error while create tour"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid put data."
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
        if (Auth::user()->can('create', Tour::class)) {

            $this->tourService->validatePostData($request);

            if ($this->tourService->allDataExists($request)) {

                if ($this->tourService->areAvailability($request)) {

                    if ($this->tourService->create($request)) {

                        return response()->json([
                            'success' => true,
                            'message' => 'Tour created'
                        ], Response::HTTP_CREATED);

                    } else {

                        return response()->json([
                            'success' => false,
                            'message' => 'Error while create tour'
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);

                    }

                } else {

                    return response()->json([
                        'success' => false,
                        'message' => 'At least one actor is not available, choose another combination'
                    ], Response::HTTP_CONFLICT);

                }

            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'Some resource not found'
                ], Response::HTTP_NOT_FOUND);

            }

        } else {

            return $this->unauthorizedUser();

        }
    }

    /**
     * @OA\Get(
     *     path="/tours/show/{limit}",
     *     summary="Get tour by  hash id",
     *     tags={"Tours"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Parameter (
     *         name="limit",
     *         in="path",
     *         required=true,
     *         description="Number of tours was needed"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Request tours.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="The tours was request"),
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
    public function show(int $limit): JsonResponse
    {
        if (Auth::user()->can('show', Tour::class)) {

            $authUserId = Auth::user()->id;
            $authUserRole = Auth::user()->role->name;

            switch ($authUserRole) {
                case 'admin':
                    if ($this->tourService->areToursIntoSystem()) {
                        return response()->json([
                            'success' => true,
                            'data' => $this->tourService->getLastTours($limit),
                            'message' => 'Last '.$limit.' tours'
                        ], Response::HTTP_OK);
                    } else {
                        return response()->json([
                            'success' => true,
                            'message' => 'No tours into system'
                        ], Response::HTTP_OK);
                    }
                case 'customer':
                    if ($this->tourService->haveThisCustomerTours($authUserId)) {
                        return response()->json([
                            'success' => true,
                            'data' => $this->tourService->getToursByCustomerId($authUserId),
                            'message' => 'All tours by customer ' . $authUserId
                        ], Response::HTTP_OK);
                    } else {
                        return response()->json([
                            'success' => true,
                            'message' => 'Customer not have tours'
                        ], Response::HTTP_OK);
                    }
                case 'owner':
                    if ($this->tourService->haveThisOwnerPropertiesWithTours($authUserId)) {
                        return response()->json([
                            'success' => true,
                            'data' => $this->tourService->getToursOfPropertiesOwnedByOwnerId($authUserId),
                            'message' => 'All tours of properties owner ' . $authUserId
                        ], Response::HTTP_OK);
                    } else {
                        return response()->json([
                            'success' => true,
                            'message' => 'Owner not have tours'
                        ], Response::HTTP_OK);
                    }
                case 'employee':
                    if ($this->tourService->haveThisEmployeeTours($authUserId)) {
                        return response()->json([
                            'success' => true,
                            'data' => $this->tourService->getToursByEmployeeId($authUserId),
                            'message' => 'All tours by employee ' . $authUserId
                        ], Response::HTTP_OK);
                    } else {
                        return response()->json([
                            'success' => true,
                            'message' => 'Employee not have tours'
                        ], Response::HTTP_OK);
                    }
            }

        } else{

            return $this->unauthorizedUser();

        }
    }

    /**
     * @OA\Get(
     *     path="/tours/{hashId}/show",
     *     summary="Get tour by  hash id",
     *     tags={"Tours"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Parameter (
     *         name="hashId",
     *         in="path",
     *         required=true,
     *         description="Hash ID of tour"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Request tours.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="Tour by hash id X"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tour not found.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Tour not found"),
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
        if (Auth::user()->can('showByHashId', Tour::class)) {

            if ($this->tourService->existsThisTourByHashId($hashId)) {

                return response()->json([
                    'success' => true,
                    'data' => $this->tourService->getTourByHashId($hashId),
                    'message' => 'Tour by hash id ' . $hashId
                ],Response::HTTP_OK);

            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'Tour not found'
                ],Response::HTTP_NOT_FOUND);

            }

        } else {

            return $this->unauthorizedUser();

        }
    }

    /**
     * @OA\Get(
     *     path="/tours/{propertyId}/show",
     *     summary="Get tours by property id",
     *     tags={"Tours"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Parameter (
     *         name="propertyId",
     *         in="path",
     *         required=true,
     *         description="Property ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Request tours.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="Tour by hash id X"),
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
     *         response=409,
     *         description="Tour is not related with user was request it.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="The property is not yours / You have no tours with this property"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Some error when retrieve tours.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Some error when retrieve tours"),
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
    public function showByPropertyId(string $propertyId): JsonResponse
    {
        if (Auth::user()->can('showByPropertyId', Tour::class)) {

            if ($this->propertyService->existsThisProperty($propertyId)) {

                switch (Auth::user()->role->name) {

                    case 'admin':
                        return response()->json([
                            'success' => true,
                            'data' => $this->tourService->getToursByPropertyId($propertyId),
                            'message' => 'All tours of property ' . $propertyId
                        ], Response::HTTP_OK);
                    case 'owner':
                        if ($this->propertyService->whichIsTheOwnerIdOfThisProperty($propertyId) === Auth::user()->id) {

                            return response()->json([
                                'success' => true,
                                'data' => $this->tourService->getToursByPropertyId($propertyId),
                                'message' => 'All tours of your property with id ' . $propertyId
                            ], Response::HTTP_OK);

                        } else {

                            return response()->json([
                                'success' => false,
                                'message' => 'The property is not yours'
                            ], Response::HTTP_CONFLICT);

                        }
                    case 'customer':
                        $customerId = Auth::user()->id;

                        if ($this->tourService->haveThisCustomerToursWithThisPropertyId($propertyId, $customerId)) {
                            return response()->json([
                                'success' => true,
                                'data' => $this->tourService->getToursByCustomerIdAndPropertyId($customerId, $propertyId),
                                'message' => 'All tours you have with property ' . $propertyId
                            ], Response::HTTP_OK);

                        } else {

                            return response()->json([
                                'success' => false,
                                'message' => 'You have no tours with this property ' . $propertyId
                            ], Response::HTTP_CONFLICT);
                        }
                    case 'employee':
                        $employeeId = Auth::user()->id;

                        if ($this->tourService->haveThisEmployeeToursWithThisPropertyId($propertyId, $employeeId)) {
                            return response()->json([
                                'success' => true,
                                'data' => $this->tourService->getToursByEmployeeIdAndPropertyId($employeeId, $propertyId),
                                'message' => 'All tours you have with property ' . $propertyId
                            ], Response::HTTP_OK);

                        } else {

                            return response()->json([
                                'success' => false,
                                'message' => 'You have no tours with this property ' . $propertyId
                            ], Response::HTTP_CONFLICT);
                        }
                    default:
                        return response()->json([
                            'success' => false,
                            'message' => 'Some error when retrieve tours',
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);
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


        // Si som owner les meves
        // Si som customer les meves
    }

    /**
     * @OA\Put(
     *     path="/tours/update",
     *     summary="Update tour",
     *     tags={"Tours"},
     *     security={{ "apiAuth": {} }},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Tour data",
     *          @OA\JsonContent(
     *             @OA\Property(property="property_id", description="Property ID", type="string", example=1),
     *             @OA\Property(property="customer_id", description="Customer ID", type="string", example=2),
     *             @OA\Property(property="employee_id", description="Employee ID", type="string", example=3),
     *             @OA\Property(property="date", description="Date", type="string", example="2021-03-01"),
     *             @OA\Property(property="time", description="Hour", type="string", example="10:00:00"),
     *          ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tour updated successfully.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="Tour updated successfully"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="There are not availability.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="There are not availability"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tour not found.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Tour not found"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error while updating tour.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Error while updating tour"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid put data."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="This tour it is not related to you / Unauthorized user.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="This tour it is not related to you / Unauthorized User"),
     *         ),
     *     )
     * )
     * @throws ValidationException
     */
    public function update(Request $request): JsonResponse
    {
        if (Auth::user()->can('update', Tour::class)) {

            $this->tourService->validateHashId($request);
            $this->tourService->validatePostData($request);

            $hashId = $request->input('hash_id');
            $authUserRole = Auth::user()->role->name;
            $authUserId = Auth::user()->id;

            if ($this->tourService->existsThisTourByHashId($hashId)) {

                if ($this->tourService->thisUserIsRelatedWithThisTour($authUserRole, $authUserId, $hashId)) {

                    if ($this->tourService->areAvailability($request)) {

                        if ($this->tourService->update($request)) {

                            return response()->json([
                                'success' => true,
                                'message' => 'Tour updated successfully'
                            ], Response::HTTP_OK);

                        } else {

                            return response()->json([
                                'success' => false,
                                'message' => 'Error while updating tour'
                            ], Response::HTTP_INTERNAL_SERVER_ERROR);

                        }

                    } else {

                        return response()->json([
                            'success' => false,
                            'message' => 'There are not availability'
                        ], Response::HTTP_CONFLICT);

                    }

                } else {

                    return response()->json([
                        'success' => false,
                        'message' => 'This tour ' . $hashId . ' it is not related to you'
                    ], Response::HTTP_UNAUTHORIZED);

                }

            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'Tour not found'
                ], Response::HTTP_NOT_FOUND);

            }

        } else {

            return $this->unauthorizedUser();

        }
    }

    /**
     * @OA\Delete(
     *     path="/tours/{hashId}/delete",
     *     summary="Delete tour by hash id",
     *     tags={"Tours"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Parameter (
     *         name="hashId",
     *         in="path",
     *         required=true,
     *         description="Hash ID of tour"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tour deleted successfully.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="Tour deleted"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error while deleting tour.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Error while deleting tour"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tour not found.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Tour not found"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Tour is not yours / Unauthorized user.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Tour is not yours / Unauthorized User"),
     *         ),
     *     )
     * )
     */
    public function destroy(string $hashId): JsonResponse
    {
        if (Auth::user()->can('destroy', Tour::class)) {

            if ($this->tourService->existsThisTourByHashId($hashId)) {

                $authUserRole = Auth::user()->role->name;
                $authUserId = Auth::user()->id;

                if ($this->tourService->thisUserIsRelatedWithThisTour($authUserRole, $authUserId, $hashId)) {

                    if ($this->tourService->delete($hashId)) {

                        return response()->json([
                            'success' => true,
                            'message' => 'Tour deleted'
                        ], Response::HTTP_OK);

                    } else {

                        return response()->json([
                            'success' => false,
                            'message' => 'Error while deleting tour'
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);

                    }

                } else {

                    return response()->json([
                        'success' => false,
                        'message' => 'Tour is not yours'
                    ], Response::HTTP_UNAUTHORIZED);

                }

            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'Tour not found'
                ], Response::HTTP_NOT_FOUND);

            }

        } else {

            return $this->unauthorizedUser();

        }
    }

    /**
     * @OA\Get(
     *     path="/tours/byEmployee",
     *     summary="Get employee tours",
     *     tags={"Tours"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Request tours of employee.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="Tour of employee with id X"),
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
    public function getToursByEmployee(): JsonResponse
    {
        if (Auth::user()->role->name === 'employee') {
            return response()->json([
                'success' => true,
                'data' => Auth::user()->makeTours()->with('customer')->with('property')->get()->toArray(),
                'message' => 'Tours of employee with id ' . Auth::id()
            ], Response::HTTP_OK);
        }
        return $this->unauthorizedUser();
    }
}
