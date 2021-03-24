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
    public function getAllSales(): array;
    public function getSalesByOwnerId(string $ownerId): array;
    public function getSalesByCustomerId(string $customerId): array;
    public function getSalesByEmployeeId(string $employeeId): array;
    public function getSaleByHashId(string $hashId, string $role, string $userId): array;
}
