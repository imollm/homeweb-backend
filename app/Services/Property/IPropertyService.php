<?php


namespace App\Services\Property;

use App\Models\Property;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

interface IPropertyService
{
    public function validatePostPropertyData(Request $request);
    public function createOrUpdateProperty(Request $request, string $action, string $propertyId = ''): bool;
    public function validateFilterPostData(Request $request);
    public function getPropertiesByFilters(Request $request): Collection | null;
    public function existsThisProperty(string $id): bool;
    public function whichIsTheOwnerIdOfThisProperty(string $id): string;
    public function getPriceHistoryOfThisProperty(string $id): array;
}
