<?php


namespace App\Services\Sale;

use App\Models\Sale;
use Illuminate\Http\Request;

interface ISaleService
{
    public function validatePostData(Request $request);
    public function allDataExists(Request $request): bool;
    public function isToSellThisProperty(Request $request): bool;
    public function create(Request $request): bool;
    public function update(Request $request): bool;
    public function getLastSales(int $limit = 3): array;
    public function getSalesByOwnerId(string $ownerId): array;
    public function getSalesByCustomerId(string $customerId): array;
    public function getSalesByEmployeeId(string $employeeId): array;
    public function getSaleByHashId(string $hashId, string $role, string $userId): array;
    public function getSalesOfActualYear(string $year): array;
    public function getSalesByCategories(): array;
    public function getSalesByCountries(): array;
    public function getSalesByCities(): array;
    public function getSalesBySellers(): array;
    public function exitsThisSale(string $hashId): bool;
    public function isThisSaleOfThisSeller(string $hashId, int $sellerId): bool;
    public function getSalesOfAuthSeller(): array;
}
