<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Services\Property\PropertyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PropertyController
 * @package App\Http\Controllers
 */
class PropertyController extends Controller
{
    /**
     * @var PropertyService
     */
    private PropertyService $propertyService;

    /**
     * PropertyController constructor.
     *
     * @param PropertyService $propertyService
     */
    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }

    /**
     * Return all models stored in database.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $activeProperties = $this->propertyService->getActiveProperties();

        return response()->json([
            'success' => true,
            'data' => $activeProperties,
            'message' => 'List of all properties',
        ], Response::HTTP_OK);
    }

    /**
     * Show a property by id
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => $property,
            'message' => 'The property was request'
        ], Response::HTTP_OK);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function showByFilter(Request $request): JsonResponse
    {
        $this->propertyService->validateFilterPostData($request);

        $result = $this->propertyService->getPropertiesByFilters($request);

        if (!is_null($result)) {
            if (count($result) > 0) {
                return response()->json([
                    'success' => true,
                    'data' => $result,
                    'message' => 'Properties request',
                ], Response::HTTP_OK);
            }
        }
        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Create a property model into database.
     * If admin or employee create this property, add owner with request owner id.
     * Otherwise, check if the owner have that role, if not return error response.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function create(Request $request): JsonResponse
    {
        if (Auth::user()->can('create', Property::class)) {

            $this->propertyService->validatePostPropertyData($request);

            if ($this->propertyService->createOrUpdateProperty($request, 'create')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Property added correctly',
                ], Response::HTTP_CREATED);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Property not added',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

        } else {
            return $this->unauthorizedUser();
        }
    }

    /**
     * Update property
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request): JsonResponse
    {
        $propertyId = $request->input('id');
        $propertyExists = $this->propertyService->getPropertyById($propertyId);

        if (!$propertyExists) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], Response::HTTP_NOT_FOUND);
        }

        if (Auth::user()->can('update', $propertyExists)) {

            $this->propertyService->validatePutPropertyData($request);

            if ($this->propertyService->createOrUpdateProperty($request, 'update', $propertyId)) {
                return response()->json([
                    'success' => true,
                    'data' => Property::find($propertyId),
                    'message' => 'Property updated successfully',
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Property can not be updated'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            return $this->unauthorizedUser();
        }
    }

    /**
     * Delete a property by id
     *
     * @param $id
     * @return JsonResponse
     */
    public function delete(string $id): JsonResponse
    {
        if (Auth::user()->can('delete', Property::class)) {

            if ($this->propertyService->existsThisProperty($id)) {

                if ($this->propertyService->delete($id)) {

                    return response()->json([], Response::HTTP_NO_CONTENT);

                } else {

                    return response()->json([
                        'success' => false,
                        'message' => 'Property can not be deleted'
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

    }

    /**
     * Set active field to display property on public
     *
     * @param string $id
     * @param string $status
     * @return JsonResponse
     */
    public function setActive(string $id, string $status): JsonResponse
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ], Response::HTTP_NOT_FOUND);
        }

        if (Auth::user()->can('setActive', $property)) {

            $property->active = (bool)$status;

            if ($property->save()) {

                return response()->json([], Response::HTTP_NO_CONTENT);

            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'Error when active/desactive property ' . $id
                ], Response::HTTP_INTERNAL_SERVER_ERROR);

            }

        } else {
            return $this->unauthorizedUser();
        }
    }

    /**
     * To get owner
     *
     * @param string $id
     * @return JsonResponse
     */
    public function owner(string $id): JsonResponse
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property can not be retrieve',
            ], Response::HTTP_NOT_FOUND);
        } else {
            $owner = $property->owner();

            if (!$owner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner can not be retrieve',
                ], Response::HTTP_NOT_FOUND);
            } else {
                return response()->json([
                    'success' => true,
                    'data' => $owner,
                ], Response::HTTP_OK);
            }
        }
    }
}
