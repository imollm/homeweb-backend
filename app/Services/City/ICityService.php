<?php


namespace App\Services\City;


use Illuminate\Http\Request;

interface ICityService
{
    public function validatePostData(Request $request);
    public function existsRelatedCountry(Request $request): bool;
    public function existsThisCityWithSameCountry(Request $request): bool;
    public function create(Request $request): bool;
}
