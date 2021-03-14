<?php


namespace App\Services\City;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class CityService
 * @package App\Services
 */
class CityService implements ICityService
{

    /**
     * @param Request $request
     * @throws ValidationException
     */
    public function validateCityPostData(Request $request)
    {
        Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'country_id' => 'required'
        ])->validate();
    }

    public function create(Request $request): bool
    {
        // TODO: Implement create() method.
    }
}
