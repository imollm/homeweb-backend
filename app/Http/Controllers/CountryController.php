<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\CountryService;
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
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $countries = Country::all();

        return response()->json([
            'success' => true,
            'data' => $countries,
            'message' => 'List of all countries',
        ], Response::HTTP_OK);
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
        if (Auth::user()->can('store', Country::class)) {

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
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
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
        $country = Country::find($id);

        if (!$country) {
            return response()->json([
                'success' => false,
                'message' => 'Country not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => $country->toArray(),
            'message' => 'Country found'
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Country $country
     * @return JsonResponse
     */
    public function update(Request $request, Country $country)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Country $country
     * @return JsonResponse
     */
    public function destroy(Country $country)
    {
        //
    }
}
