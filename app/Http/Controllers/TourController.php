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
     * Display a listing of the resource.
     *
     * @return JsonResponse
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
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        if (Auth::user()->can('store', Tour::class)) {

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
                        'message' => 'At least one actor is not available'
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
     * Display the specified resource.
     *
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        if (Auth::user()->can('show', Tour::class)) {

            $authUserId = Auth::user()->id;
            $authUserRole = Auth::user()->role->name;

            switch ($authUserRole) {
                case 'admin':
                    if ($this->tourService->areToursIntoSystem()) {
                        return response()->json([
                            'success' => true,
                            'data' => $this->tourService->getLastTours(),
                            'message' => 'Last tours'
                        ], Response::HTTP_OK);
                    } else {
                        return response()->json([], Response::HTTP_NO_CONTENT);
                    }
                case 'customer':
                    if ($this->tourService->haveThisCustomerTours($authUserId)) {
                        return response()->json([
                            'success' => true,
                            'data' => $this->tourService->getToursByCustomerId($authUserId),
                            'message' => 'All tours by customer ' . $authUserId
                        ], Response::HTTP_OK);
                    } else {
                        return response()->json([], Response::HTTP_NO_CONTENT);
                    }
                case 'owner':
                    if ($this->tourService->haveThisOwnerPropertiesWithTours($authUserId)) {
                        return response()->json([
                            'success' => true,
                            'data' => $this->tourService->getToursOfPropertiesOwnedByOwnerId($authUserId),
                            'message' => 'All tours of properties owner ' . $authUserId
                        ], Response::HTTP_OK);
                    } else {
                        return response()->json([], Response::HTTP_NO_CONTENT);
                    }
                case 'employee':
                    if ($this->tourService->haveThisEmployeeTours($authUserId)) {
                        return response()->json([
                            'success' => true,
                            'data' => $this->tourService->getToursByEmployeeId($authUserId),
                            'message' => 'All tours by employee ' . $authUserId
                        ], Response::HTTP_OK);
                    } else {
                        return response()->json([], Response::HTTP_NO_CONTENT);
                    }
            }

        } else{

            return $this->unauthorizedUser();

        }
    }

    /**
     * @param string $hashId
     * @return JsonResponse
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
     * Display the specified resource.
     *
     * @param string $propertyId
     * @return JsonResponse
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
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
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
     * Remove the specified resource from storage.
     *
     * @param string $hashId
     * @return JsonResponse
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
}
