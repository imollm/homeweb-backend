<?php


namespace App\Services\Property;

use App\Models\Property;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

interface IPropertyService
{
    public function validatePostPropertyData(Request $request);
    public function validatePutPropertyData(Request $request);
    public function createOrUpdateProperty(Request $request, string $action, string $propertyId = ''): bool;
    public function validateFilterPostData(Request $request);
    public function getPropertiesByFilters(Request $request): Collection | null;
    public function existsThisProperty(string $id): bool;
    public function whichIsTheOwnerIdOfThisProperty(string $id): int;
    public function getPriceHistoryOfThisProperty(string $id): array;
    public function updatePriceByPropertyId(string $propertyId, float $price): bool;
    public function getActiveProperties(): array;
    public function delete(string $id): bool;
    public function getPropertyById(string $id): Property | false;
    public function getLastProperties(int $count): array;
    public function getAllProperties(): array;
    public function getLastActiveProperties(int $count): array;
    public function getForSaleProperties(): array;
}
