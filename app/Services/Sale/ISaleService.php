<?php


namespace App\Services\Sale;

use App\Models\Sale;
use Illuminate\Http\Request;

interface ISaleService
{
    public function validatePostData(Request $request);
    public function allDataExists(Request $request): bool;
    public function theBuyerAndTheOwnerAreTheSame(string $buyerId, string $propertyId): bool;
    public function theBuyerHadAlreadyBoughtThisProperty(string $buyerId, string $propertyId): Sale | null;
    public function theSaleDateIsHigherThanThenPreviousSaleDate(string $saleDate, Sale $previousSale): bool;
    public function create(Request $request): bool;
}
