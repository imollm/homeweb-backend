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
}
