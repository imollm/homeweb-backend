<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\User;
use App\Services\PropertyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use function PHPUnit\Framework\isNull;

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
    public function all(): JsonResponse
    {
        $properties = Property::all();

        return response()->json([
            'success' => true,
            'data' => $properties,
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

        $ref = $request->input('reference');
        $price = $request->input('price');
        $location = $request->input('location');
        $category = $request->input('category');

        $result = $this->propertyService->getPropertiesByFilters($ref, $price, $location, $category);

        return response()->json([
            'success' => true,
            'data' => $result,
            'message' => 'Properties request'
        ], Response::HTTP_OK);
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
     * @param $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $propertyExists = Property::find($id);

        if (!$propertyExists) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], Response::HTTP_NOT_FOUND);
        }

        if (Auth::user()->can('update', $propertyExists)) {

            $this->propertyService->validatePostPropertyData($request);

            if ($this->propertyService->createOrUpdateProperty($request, 'update', $id)) {
                return response()->json([
                    'success' => true,
                    'data' => Property::find($id),
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
    public function delete($id): JsonResponse
    {
        $property = Property::find($id);
        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], Response::HTTP_NOT_FOUND);
        }

        if (Auth::user()->can('delete')) {

            if ($property->delete()) {

                return response()->json([
                    'success' => true
                ], Response::HTTP_OK);

            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'Post can not be deleted'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
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

        if (!$property)
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ], Response::HTTP_NOT_FOUND);

        if (Auth::user()->can('setActive', $property)) {

            $property->active = (bool)$status;
            $property->save();

            return response()->json([
                'success' => true,
            ], Response::HTTP_NO_CONTENT);

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
