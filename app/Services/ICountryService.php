<?php


namespace App\Services;


use App\Models\Country;
use Illuminate\Http\Request;

interface ICountryService
{
    public function validatePostData(Request $request);
    public function create(Request $request): bool;
    public function existsThisCountry(string $id): Country | null;
    public function update(Request $request): bool;
    public function delete(Country $country): bool;
    public function hasThisCountryAnyCityRelated(Country $country): bool;
}
