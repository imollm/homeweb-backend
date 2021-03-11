<?php


namespace App\Services;

use Illuminate\Http\Request;

interface PropertyServiceI
{
    public function validatePostPropertyData(Request $request);
    public function createOrUpdateProperty(Request $request, string $action, string $propertyId = ''): bool;
    public function validateFilterPostData(Request $request);
    public function getPropertiesByFilters(string $ref, string $price, string $location, string $category): array | null;
}
