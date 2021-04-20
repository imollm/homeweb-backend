<?php


namespace App\Services\Country;


use App\Models\Country;
use Illuminate\Http\Request;

interface ICountryService
{
    public function validatePostData(Request $request);
    public function create(Request $request): bool;
    public function existsThisCountry(string $id): Country | bool;
    public function update(Request $request): bool;
    public function delete(Country $country): bool;
    public function hasThisCountryAnyCityRelated(Country $country): bool;
    public function getAllCountries(): array;
    public function getCountryById(string $id): array;
    public function getCities(string $id): array;
    public function getProperties(string $id): array;
}
