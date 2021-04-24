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
     * @var City
     */
    private City $city;

    private Country $country;

    /**
     * CityService constructor.
     * @param City $city
     * @param Country $country
     */
    public function __construct(City $city, Country $country)
    {
        $this->city = $city;
        $this->country = $country;
    }

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

    /**
     * @param string $id
     * @return bool
     */
    public function existThisCity(string $id): bool
    {
        $city = $this->city->find($id);

        return !is_null($city);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function existsRelatedCountry(Request $request): bool
    {
        $countryId = $request->input('country_id');

        return !is_null($this->country->find($countryId));
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function existsThisCityWithSameCountry(Request $request): bool
    {
        $countryId = $request->input('country_id');
        $name = $request->input('name');

        $cityAlreadyExists = $this->city->where('name', $name)->where('country_id', $countryId)->get()->first();

        return is_null($cityAlreadyExists);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function create(Request $request): bool
    {
        $country = $this->city->create([
            'country_id' => $request->input('country_id'),
            'name' => $request->input('name'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude')
        ]);

        return $country ? true : false;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function update(Request $request): bool
    {
        $cityId = $request->input('id');
        $cityName = $request->input('name');
        $cityCountryId = $request->input('country_id');

        $city = $this->city->where('id', $cityId)
                        ->update([
                            'name' => $cityName,
                            'country_id' => $cityCountryId
                        ]);

        return !is_null($city);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function hasThisCityRelatedProperties(string $id): bool
    {
        return count($this->city->find($id)->properties) > 0 ? false : true;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool
    {
        return $this->city->find($id)->delete();
    }

    /**
     * @return array
     */
    public function getAllCities(): array
    {
        $cities = $this->city->with('propertiesCount')->with('country')->orderBy('country_id', 'ASC')->get();

        return !is_null($cities) ? $cities->toArray() : [];
    }

    /**
     * @param string $id
     * @return array
     */
    public function getCityById(string $id): array
    {
        $city = $this->city->whereId($id)->with('propertiesCount')->with('country')->with('properties')->get();

        return !is_null($city) ? $city->toArray() : [];
    }
}
