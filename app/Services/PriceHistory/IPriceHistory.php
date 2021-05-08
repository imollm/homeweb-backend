<?php

namespace App\Services\PriceHistory;

use Illuminate\Http\Request;

interface IPriceHistory
{
    public function validatePostData(Request $request);
    public function hasThisPropertyTheSamePriceChange(Request $request): bool;
    public function startTimestampGivenIsGreaterThanLast(Request $request): bool;
    public function create(Request $request): bool;
    public function areYouAllowedToStoreAPriceChangeOfThisProperty(Request $request): bool;
    public function getAllChanges(): array;
}
