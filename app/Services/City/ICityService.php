<?php


namespace App\Services\City;


use Illuminate\Http\Request;

interface ICityService
{
    public function validatePostData(Request $request);
    public function create(Request $request): bool;
}
