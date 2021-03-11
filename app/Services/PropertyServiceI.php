<?php


namespace App\Services;

use App\Models\Property;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

interface PropertyServiceI
{
    public function validatePostPropertyData(Request $request);
    public function createOrUpdateProperty(Request $request, string $action, string $propertyId = ''): bool;
    public function validateFilterPostData(Request $request);
    public function getPropertiesByFilters(string $ref = null, string $lowPrice = null, float $upperPrice = null, string $location = null, string $category = null): Collection | null;
}
