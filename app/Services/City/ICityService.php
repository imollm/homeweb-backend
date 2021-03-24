<?php


namespace App\Services\City;


use Illuminate\Http\Request;

interface ICityService
{
    public function validatePostData(Request $request);
    public function existThisCity(string $id): bool;
    public function existsRelatedCountry(Request $request): bool;
    public function existsThisCityWithSameCountry(Request $request): bool;
    public function create(Request $request): bool;
    public function hasThisCityRelatedProperties(string $id): bool;
    public function delete(string $id): bool;
    public function getAllCities(): array;
    public function getCityById(string $id): array;
}
