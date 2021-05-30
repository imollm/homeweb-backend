<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Services\City\CityService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CityController
 * @package App\Http\Controllers
 */
class CityController extends Controller
{
    /**
     * @var CityService
     */
    private CityService $cityService;

    /**
     * CountryController constructor.
     * @param CityService $cityService
     */
    public function __construct(CityService $cityService)
    {
        $this->cityService = $cityService;
    }

    /**
     * @OA\Get(
     *     path="/cities/index",
     *     summary="Get all cities",
     *     tags={"Cities"},
     *     @OA\Response(
     *         response=200,
     *         description="List of all cities.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="List of all cities"),
     *         ),
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->cityService->getAllCities(),
            'message' => 'List of all cities'
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/cities/create",
     *     summary="Store new city",
     *     tags={"Cities"},
     *     security={{ "apiAuth": {} }},
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema (
     *                  @OA\Property (
     *                      property="name",
     *                      type="string",
     *                      example="Foo"
     *                  ),
     *                  @OA\Property (
     *                      property="country_id",
     *                      type="integer",
     *                      example=1
     *                  ),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="City created.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="City created"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="City already exists with same country.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="City already exists with same country"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error when create city.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Error when create city"),
     *         ),
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Related country not found.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Related country not found"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized user.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Unauthorized user"),
     *         ),
     *     )
     * )
     */
    public function create(Request $request): JsonResponse
    {
        if (Auth::user()->can('create', City::class)) {

            $this->cityService->validatePostData($request);

            if ($this->cityService->existsRelatedCountry($request)) {

                if ($this->cityService->existsThisCityWithSameCountry($request)) {

                    if ($this->cityService->create($request)) {

                        return response()->json([
                            'success' => true,
                            'message' => 'City created'
                        ], Response::HTTP_CREATED);

                    } else {

                        return response()->json([
                            'success' => false,
                            'message' => 'Error when create city'
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);

                    }

                } else {

                    return response()->json([
                        'success' => false,
                        'message' => 'City already exists with same country'
                    ], Response::HTTP_CONFLICT);

                }

            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'Related country not found'
                ], Response::HTTP_NOT_FOUND);

            }

        } else {

            return $this->unauthorizedUser();

        }
    }

    /**
     * @OA\Get(
     *     path="/cities/{id}/show",
     *     summary="Get city by id",
     *     tags={"Cities"},
     *     @OA\Parameter (
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of city"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="City found.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object", example="{}"),
     *             @OA\Property (property="message", type="string", example="City found"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="City not found.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="City not found"),
     *         ),
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        if ($this->cityService->existThisCity($id)) {

            return response()->json([
                'success' => true,
                'data' => $this->cityService->getCityById($id),
                'message' => 'City found'
            ], Response::HTTP_OK);

        } else {

            return response()->json([
                'success' => false,
                'message' => 'City not found'
            ], Response::HTTP_NOT_FOUND);

        }
    }

    /**
     * @OA\Put(
     *     path="/cities/update",
     *     summary="Update city",
     *     tags={"Cities"},
     *     security={{ "apiAuth": {} }},
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema (
     *                  @OA\Property (
     *                      property="name",
     *                      type="string",
     *                      example="Foo"
     *                  ),
     *                  @OA\Property (
     *                      property="country_id",
     *                      type="integer",
     *                      example=1
     *                  ),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="City updated successfully.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="City updated successfully"),
     *         ),
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Error while update city.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Error while update city"),
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
        if (Auth::user()->can('update', City::class)) {

            $this->cityService->validatePostData($request);

            if ($this->cityService->update($request)) {

                return response()->json([
                    'success' => true,
                    'message' => 'City updated successfully'
                ], Response::HTTP_OK);

            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'Error while update city'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);

            }

        } else {

            return $this->unauthorizedUser();

        }
    }

    /**
     * @OA\Delete(
     *     path="/cities/{id}/delete",
     *     summary="Delete city by id",
     *     tags={"Cities"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Parameter (
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of city"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="City deleted.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="City deleted"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error while delete city.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Error while delete city"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Error, this city has properties related.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Error, this city has properties related"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="City not found.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="City not found"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized user.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Unauthorized user"),
     *         ),
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        if (Auth::user()->can('destroy', City::class)) {

            if ($this->cityService->existThisCity($id)) {

                if ($this->cityService->hasThisCityRelatedProperties($id)) {

                    if ($this->cityService->delete($id)) {

                        return response()->json([
                            'success' => true,
                            'message' => 'City deleted'
                        ], Response::HTTP_OK);

                    } else {

                        return response()->json([
                            'success' => false,
                            'message' => 'Error while delete city'
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);

                    }

                } else {

                    return response()->json([
                        'success' => false,
                        'message' => 'Error, this city has properties related'
                    ], Response::HTTP_CONFLICT);

                }

            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'City not found'
                ], Response::HTTP_NOT_FOUND);

            }

        } else {

            return $this->unauthorizedUser();

        }
    }
}
