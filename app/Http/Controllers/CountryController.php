<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\Country\CountryService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Constraint\Count;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CountryController
 * @package App\Http\Controllers
 */
class CountryController extends Controller
{
    /**
     * @var CountryService
     */
    private CountryService $countryService;

    /**
     * CountryController constructor.
     * @param CountryService $countryService
     */
    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    /**
     * @OA\Get(
     *     path="/countries/index",
     *     summary="Get all countries",
     *     tags={"Countries"},
     *     @OA\Response(
     *         response=200,
     *         description="All categories.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="List of all countries"),
     *         ),
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->countryService->getAllCountries(),
            'message' => 'List of all countries',
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/countries/create",
     *     summary="Store new country",
     *     tags={"Countries"},
     *     security={{ "apiAuth": {} }},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Country data",
     *          @OA\JsonContent(
     *             @OA\Property(property="name", description="Name of category", type="string", example="Foo"),
     *             @OA\Property(property="code", description="Code of country", type="string", example="FOO"),
     *          ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Country created.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="Country created"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Country already exists.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Country already exists"),
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
        if (Auth::user()->can('create', Country::class)) {

            $this->countryService->validatePostData($request);

            if ($this->countryService->create($request)) {

                return response()->json([
                    'success' => true,
                    'message' => 'Country created'
                ], Response::HTTP_CREATED);

            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'Country already exists'
                ], Response::HTTP_CONFLICT);
            }
        } else {
            return $this->unauthorizedUser();
        }
    }

    /**
     * @OA\Get(
     *     path="/countries/{id}/show",
     *     summary="Get country by id",
     *     tags={"Countries"},
     *     @OA\Parameter (
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of category"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Country found.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="Country found"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Country not found.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Country not found"),
     *         ),
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        if ($this->countryService->existsThisCountry($id)) {
            return response()->json([
                'success' => true,
                'data' => $this->countryService->getCountryById($id),
                'message' => 'Country found'
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Country not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @OA\Put(
     *     path="/countries/update",
     *     summary="Update category",
     *     tags={"Countries"},
     *     security={{ "apiAuth": {} }},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Country data",
     *          @OA\JsonContent(
     *             @OA\Property(property="name", description="Name of category", type="string", example="Foo"),
     *             @OA\Property(property="code", description="Code of country", type="string", example="FOO"),
     *          ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Country updated.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="Country updated"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error while update country.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Error while update country"),
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
    public function update(Request $request): JsonResponse
    {
        if (Auth::user()->can('update', Country::class)) {

            $this->countryService->validatePostData($request);

            if ($this->countryService->update($request)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Country updated'
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while update country'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            return $this->unauthorizedUser();
        }
    }

    /**
     * @OA\Delete(
     *     path="/countries/{id}/delete",
     *     summary="Delete country by id",
     *     tags={"Countries"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Parameter (
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of country"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Country deleted.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="Country deleted"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="The country can not be deleted.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="The country can not be deleted"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="The country have cities related.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="The country have cities related"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="The country can not be found.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="The country can not be found"),
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
    public function destroy(string $id): JsonResponse
    {
        if (Auth::user()->can('destroy', Country::class)) {

            if ($country = $this->countryService->existsThisCountry($id)) {

                if (!$this->countryService->hasThisCountryAnyCityRelated($country)) {

                    if ($this->countryService->delete($country)) {

                        return response()->json([
                            'success' => true,
                            'message' => 'Country deleted'
                        ], Response::HTTP_OK);

                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'The country can not be deleted'
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }

                } else {

                    return response()->json([
                        'success' => false,
                        'message' => 'The country have cities related'
                    ], Response::HTTP_CONFLICT);
                }
            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'The country can not be found'
                ], Response::HTTP_NOT_FOUND);

            }
        } else {
            return $this->unauthorizedUser();
        }
    }

    /**
     * @OA\Get(
     *     path="/countries/{id}/citiesAndProperties",
     *     summary="Get all properties by category name",
     *     tags={"Countries"},
     *     @OA\Parameter (
     *         name="name",
     *         in="path",
     *         required=true,
     *         description="ID of country"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cities and properties of country with id {id}.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="Cities and properties of country with id {id}"),
     *         ),
     *     )
     * )
     */
    public function getCitiesAndProperties(string $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->countryService->getCitiesAndProperties($id),
            'message' => 'Cities and properties of country with id ' . $id
        ], Response::HTTP_OK);
    }
}
