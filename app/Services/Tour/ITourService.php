<?php


namespace App\Services\Tour;

use Illuminate\Http\Request;

interface ITourService
{
    public function validatePostData(Request $request);
    public function allDataExists(Request $request): bool;
    public function areAvailability(Request $request): bool;
    public function create(Request $request): bool;
    public function update(Request $request): bool;
    public function delete(string $hashId): bool;
    public function areToursIntoSystem(): bool;

    public function validateHashId(Request $request);
    public function thisUserIsRelatedWithThisTour(string $role, string $userId, string $hashId): bool;
    public function getTourByHashId(string $hashId): array;
    public function existsThisTourByHashId(string $hashId): bool;

    public function getToursByPropertyId(string $propertyId): array;
    public function getAllTours(): array;
    public function getLastTours(int $limit = 3): array;

    public function haveThisCustomerTours(string $customerId): bool;
    public function getToursByCustomerId(string $customerId): array;

    public function haveThisEmployeeTours(string $employeeId): bool;
    public function getToursByEmployeeId(string $employeeId): array;

    public function haveThisOwnerPropertiesWithTours(string $ownerId): bool;
    public function getToursOfPropertiesOwnedByOwnerId(string $ownerId): array;

    public function haveThisCustomerToursWithThisPropertyId(string $propertyId, string $customerId): bool;
    public function getToursByCustomerIdAndPropertyId(string $customerId, string $propertyId): array;

    public function haveThisEmployeeToursWithThisPropertyId(string $propertyId, string $employeeId): bool;
    public function getToursByEmployeeIdAndPropertyId(string $employeeId, string $propertyId): array;

}
