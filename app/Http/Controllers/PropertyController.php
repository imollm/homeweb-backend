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
     * @OA\Get(
     *     path="/properties/index",
     *     summary="Get all properties",
     *     tags={"Properties"},
     *     @OA\Response(
     *         response=200,
     *         description="List of all activated properties.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="List of all activated properties"),
     *         ),
     *     ),
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->propertyService->getAllProperties(),
            'message' => 'List of all activated properties',
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/properties/{id}/show",
     *     summary="Get property by id",
     *     tags={"Properties"},
     *     @OA\Parameter (
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of property"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="The property was request",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="The property was request"),
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
     * )
     */
    public function show(string $id): JsonResponse
    {
        $property = $this->propertyService->getPropertyById($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => $property->toArray(),
            'message' => 'The property was request'
        ], Response::HTTP_OK);

    }

    /**
     * @OA\Get(
     *     path="/properties/showByFilter",
     *     summary="Get property by id",
     *     tags={"Properties"},
     *     @OA\Parameter (
     *         name="reference",
     *         in="query",
     *         required=true,
     *         description="Reference of property"
     *     ),
     *     @OA\Parameter (
     *         name="price",
     *         in="query",
     *         required=true,
     *         description="Range price ID, for example 1 is 100.000 to 200.000. For more clarification go to range_prices table on DB."
     *     ),
     *     @OA\Parameter (
     *         name="location",
     *         in="query",
     *         required=true,
     *         description="City ID"
     *     ),
     *     @OA\Parameter (
     *         name="category",
     *         in="query",
     *         required=true,
     *         description="Category ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Result of filter",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="Properties request"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid put data."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Properties not found with this filters.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="Properties not found with this filters"),
     *         ),
     *     ),
     * )
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
        return response()->json([
            'success' => true,
            'message' => 'Properties not found with this filters'
        ], Response::HTTP_NOT_FOUND);
    }

    /**
     * @OA\Post(
     *     path="/properties/create",
     *     summary="Store new property",
     *     tags={"Properties"},
     *     security={{ "apiAuth": {} }},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Property data",
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema (
     *                  @OA\Property(property="user_id", description="Owner ID", type="integer", example=1),
     *                  @OA\Property(property="category_id", description="Cateogry ID", type="integer", example=1),
     *                  @OA\Property(property="city_id", description="City ID", type="integer", example=1),
     *                  @OA\Property(property="title", description="Property title", type="string", example="A beautiful house."),
     *                  @OA\Property(property="reference", description="Reference of property, is UNIQUE", type="string", example="ABC123"),
     *                  @OA\Property(property="image[]", description="Image of property", type="array", @OA\Items(type="string", format="binary")),
     *                  @OA\Property(property="plot_meters", description="Plot meters of property", type="number", example=300.25),
     *                  @OA\Property(property="built_meters", description="Built meters of property", type="number", example=200),
     *                  @OA\Property(property="rooms", description="Number of rooms", type="integer", example=3),
     *                  @OA\Property(property="baths", description="Number of baths", type="integer", example=2),
     *                  @OA\Property(property="address", description="Address of property", type="string", example="St. Homeweb, 123"),
     *                  @OA\Property(property="longitude", description="Geolocation coordinate longitude", type="string", example="1.939796"),
     *                  @OA\Property(property="latitude", description="Geolocation coordinate latitude", type="string", example="41.636433"),
     *                  @OA\Property(property="description", description="Description of property", type="string", example="With sea views it is one of the most well located and beautiful"),
     *                  @OA\Property(property="energetic_certification", description="Status of energetic certification, values must be: obtingut, en proces i pendent", type="string", example="pendent"),
     *                  @OA\Property(property="sold", description="Specify if property is sold", type="boolean", example=true),
     *                  @OA\Property(property="active", description="Specify if property is visible on public web", type="boolean", example=true),
     *                  @OA\Property(property="price", description="Initial price of property", type="string", example="100000"),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Property created.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="Property added correctly"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Property not created.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Property not added"),
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
     * @OA\Post(
     *     path="/sales/create",
     *     summary="Update property",
     *     tags={"Properties"},
     *     security={{ "apiAuth": {} }},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Property data",
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema (
     *                  @OA\Property(property="id", description="Property ID", type="integer", example=1),
     *                  @OA\Property(property="user_id", description="Owner ID", type="integer", example=1),
     *                  @OA\Property(property="category_id", description="Cateogry ID", type="integer", example=1),
     *                  @OA\Property(property="city_id", description="City ID", type="integer", example=1),
     *                  @OA\Property(property="title", description="Property title", type="string", example="A beautiful house."),
     *                  @OA\Property(property="reference", description="Reference of property, is UNIQUE", type="string", example="ABC123"),
     *                  @OA\Property(property="image[]", description="Image of property", type="array", @OA\Items(type="string", format="binary")),
     *                  @OA\Property(property="plot_meters", description="Plot meters of property", type="number", example=300.25),
     *                  @OA\Property(property="built_meters", description="Built meters of property", type="number", example=200),
     *                  @OA\Property(property="rooms", description="Number of rooms", type="integer", example=3),
     *                  @OA\Property(property="baths", description="Number of baths", type="integer", example=2),
     *                  @OA\Property(property="address", description="Address of property", type="string", example="St. Homeweb, 123"),
     *                  @OA\Property(property="longitude", description="Geolocation coordinate longitude", type="string", example="1.939796"),
     *                  @OA\Property(property="latitude", description="Geolocation coordinate latitude", type="string", example="41.636433"),
     *                  @OA\Property(property="description", description="Description of property", type="string", example="With sea views it is one of the most well located and beautiful"),
     *                  @OA\Property(property="energetic_certification", description="Status of energetic certification, values must be: obtingut, en proces i pendent", type="string", example="pendent"),
     *                  @OA\Property(property="sold", description="Specify if property is sold", type="boolean", example=true),
     *                  @OA\Property(property="active", description="Specify if property is visible on public web", type="boolean", example=true),
     *                  @OA\Property(property="price", description="Initial price of property", type="string", example="100000"),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Property updated successfully.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="Property updated successfully"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Property can not be updated.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Property can not be updated"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid put data."
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
     *         description="Unauthorized user.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Unauthorized User"),
     *         ),
     *     )
     * )
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
     * @OA\Delete(
     *     path="/properties/{id}/delete",
     *     summary="Delete property by id",
     *     tags={"Properties"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Parameter (
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of property"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Property deleted successfully.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="Property deleted successfully"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Property can not be deleted, it info can not be deleted.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Property can not be deleted, it info can not be deleted"),
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
     *         description="Unauthorized user.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Unauthorized User"),
     *         ),
     *     )
     * )
     */
    public function delete(string $id): JsonResponse
    {
        if (Auth::user()->can('delete', Property::class)) {

            if ($this->propertyService->existsThisProperty($id)) {

                if ($this->propertyService->delete($id)) {

                    return response()->json([
                        'success' => true,
                        'message' => 'Property deleted successfully'
                    ], Response::HTTP_OK);

                } else {

                    return response()->json([
                        'success' => false,
                        'message' => 'Property can not be deleted, it info can not be deleted'
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
     * @OA\Get(
     *     path="/properties/{id}/setActive/{status}",
     *     summary="Change visibility of property",
     *     tags={"Properties"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Parameter (
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of property"
     *     ),
     *     @OA\Parameter (
     *         name="status",
     *         in="path",
     *         required=true,
     *         description="Status of visibility"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Visibility was toggled.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="Visibility was toggled"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error when active/desactive property X.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Error when active/desactive property X"),
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
     *         description="Unauthorized user.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Unauthorized User"),
     *         ),
     *     )
     * )
     */
    public function setActive(string $id, string $status): JsonResponse
    {
        if (Auth::user()->can('setActive', Property::class)) {

            $property = Property::find($id);

            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property not found',
                ], Response::HTTP_NOT_FOUND);
            }

            $property->active = $status === '1';

            if ($property->save()) {

                return response()->json([
                    'success' => true,
                    'message' => 'Visibility was toggled'
                ], Response::HTTP_OK);

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
     * @OA\Get(
     *     path="/properties/{id}/owner",
     *     summary="Get owner of property if have it",
     *     tags={"Properties"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Parameter (
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of property"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Owner of property.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="Owner of property with id X"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Property not found / Owner can not be retrieve.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Property not found / Owner can not be retrieve"),
     *         ),
     *     ),
     * )
     */
    public function owner(string $id): JsonResponse
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
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
                    'message' => 'Owner of property with id ' . $id
                ], Response::HTTP_OK);
            }
        }
    }

    /**
     * @OA\Get(
     *     path="/properties/last",
     *     summary="Get last 6 properties added.",
     *     tags={"Properties"},
     *     @OA\Response(
     *         response=200,
     *         description="Last 6 properties.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="Last 6 properties"),
     *         ),
     *     ),
     * )
     */
    public function last(): JsonResponse
    {
        $count = 6;

        return response()->json([
            'success' => true,
            'data' => $this->propertyService->getLastProperties($count),
            'message' => 'Last '.$count.' properties'
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/properties/active",
     *     summary="Get all properties visible.",
     *     tags={"Properties"},
     *     @OA\Response(
     *         response=200,
     *         description="Active properties on system.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="Active properties on system"),
     *         ),
     *     ),
     * )
     */
    public function active(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->propertyService->getActiveProperties(),
            'message' => 'Active properties on system'
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/properties/lastActive",
     *     summary="Get last 6 properties added and visible.",
     *     tags={"Properties"},
     *     @OA\Response(
     *         response=200,
     *         description="Last active properties.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="Last active properties"),
     *         ),
     *     ),
     * )
     */
    public function lastActive(): JsonResponse
    {
        $count = 6;

        return response()->json([
            'success' => true,
            'data' => $this->propertyService->getLastActiveProperties($count),
            'message' => 'Last active properties'
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/properties/forSale",
     *     summary="Get properties for sale.",
     *     tags={"Properties"},
     *     @OA\Response(
     *         response=200,
     *         description="All properties for sale.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="All properties for sale"),
     *         ),
     *     ),
     * )
     */
    public function getForSale(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->propertyService->getForSaleProperties(),
            'message' => 'All properties for sale'
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/properties/WithLimit/{limit}",
     *     summary="Get property by id",
     *     tags={"Properties"},
     *     @OA\Parameter (
     *         name="limit",
     *         in="path",
     *         required=true,
     *         description="Limit of last properties we can get"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Last X properties on system",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="Last X properties on system"),
     *         ),
     *     ),
     * )
     */
    public function getPropertiesWithLimit(string $limit): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->propertyService->getPropertiesWithLimit($limit),
            'message' => 'Last ' . $limit . ' properties on system'
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/properties/getPropertiesOwnedByOwner",
     *     summary="Get properties owned by owner",
     *     tags={"Properties"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Properties owned by owner with id X",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="Properties owned by owner with id X"),
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
    public function getPropertiesOfAuthOwner(): JsonResponse
    {
        if (Auth::user()->role->name === 'owner') {
            return response()->json([
                'success' => true,
                'data' => $this->propertyService->getPropertiesOwnedByAuthOwner(),
                'message' => 'Properties owned by owner with id ' . Auth::id()
            ]);
        } else {
            return $this->unauthorizedUser();
        }
    }
}
