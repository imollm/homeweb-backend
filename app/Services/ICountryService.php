<?php


namespace App\Services;


use Illuminate\Http\Request;

interface ICountryService
{
    public function validatePostData(Request $request);
    public function create(Request $request): bool;
}
