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
     * Display a listing of the resource.
     *
     * @return JsonResponse
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
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function create(Request $request): JsonResponse
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
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
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
                    'message' => 'Error PUT data'
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
        if (Auth::user()->can('destroy', Country::class)) {

            if ($country = $this->countryService->existsThisCountry($id)) {

                if (!$this->countryService->hasThisCountryAnyCityRelated($country)) {

                    if ($this->countryService->delete($country)) {

                        return response()->json([], Response::HTTP_NO_CONTENT);

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

    public function getCitiesAndProperties(string $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->countryService->getCitiesAndProperties($id),
            'message' => 'Cities and properties of country with id ' . $id
        ], Response::HTTP_OK);
    }
}
