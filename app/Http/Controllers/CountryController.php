<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\CountryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PHPUnit\Framework\Constraint\Count;

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
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Country $country
     * @return JsonResponse
     */
    public function show(Country $country)
    {
        //
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
