<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Services\City\CityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

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
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {

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
        if (Auth::user()->can('store', City::class)) {

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
     * Display the specified resource.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {

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
        if (Auth::user()->can('update', City::class)) {

            $this->cityService->validatePostData($request);

            if ($this->cityService->update($request)) {

                return response()->json([], Response::HTTP_NO_CONTENT);

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
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(string $id): JsonResponse
    {

    }
}
