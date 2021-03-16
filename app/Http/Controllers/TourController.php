<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class TourController
 * @package App\Http\Controllers
 */
class TourController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param string $propertyId
     * @return JsonResponse
     */
    public function showByPropertyId(string $propertyId): JsonResponse
    {

    }

    /**
     * Display the specified resource.
     *
     * @param string $employeeId
     * @return JsonResponse
     */
    public function showByEmployeeId(string $employeeId): JsonResponse
    {

    }

    /**
     * Display the specified resource.
     *
     * @param string $customerId
     * @return JsonResponse
     */
    public function showByCustomerId(string $customerId): JsonResponse
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        //
    }
}
