<?php


namespace App\Services\City;


use App\Models\City;
use App\Models\Country;
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
    public function validatePostData(Request $request)
    {
        Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'country_id' => 'required'
        ])->validate();
    }

    public function existThisCity(string $id): bool
    {
        $city = City::find($id);

        return !is_null($city);
    }

    public function existsRelatedCountry(Request $request): bool
    {
        $countryId = $request->input('country_id');

        return !is_null(Country::find($countryId));
    }

    public function existsThisCityWithSameCountry(Request $request): bool
    {
        $countryId = $request->input('country_id');
        $name = strtolower($request->input('name'));

        $cityAlreadyExists = City::where('name', $name)->where('country_id', $countryId)->get()->first();

        return is_null($cityAlreadyExists);
    }

    public function create(Request $request): bool
    {
        $country = City::create([
            'country_id' => $request->input('country_id'),
            'name' => strtolower($request->input('name'))
        ]);

        return $country ? true : false;
    }

    public function update(Request $request): bool
    {
        $cityId = $request->input('id');
        $cityName = $request->input('name');
        $cityCountryId = $request->input('country_id');

        $city = City::where('id', $cityId)
                        ->update([
                            'name' => $cityName,
                            'country_id' => $cityCountryId
                        ]);

        return !is_null($city);
    }

    public function hasThisCityRelatedProperties(string $id): bool
    {
        return count(City::find($id)->properties) > 0 ? false : true;
    }

    public function delete(string $id): bool
    {
        return City::find($id)->delete();
    }
}
