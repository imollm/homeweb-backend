<?php


namespace App\Services;


use App\Models\Property;
use Illuminate\Http\Request;

interface PropertyServiceI
{
    function validatePostPropertyData(Request $request);

    function createOrUpdateProperty(Request $request, string $action, string $propertyId = ''): bool;
}
