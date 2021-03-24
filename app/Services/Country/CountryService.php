<?php


namespace App\Services\Country;


use App\Models\Country;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class CountryService
 * @package App\Services
 */
class CountryService implements ICountryService
{
    /**
     * @var Country
     */
    private Country $country;

    /**
     * CountryService constructor.
     * @param Country $country
     */
    public function __construct(Country $country)
    {
        $this->country = $country;
    }

    /**
     * @param Request $request
     * @throws ValidationException
     */
    public function validatePostData(Request $request)
    {
        Validator::make($request->all(), [
            'code' => 'required|string|max:3',
            'name' => 'required|string|max:255'
        ])->validate();
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function create(Request $request): bool
    {
        $code = $request->input('code');
        $name = $request->input('name');

        if (!is_null($this->country->where('code', '=', $code)->get()->first())) {

            return false;

        } else {

            $this->country->create([
                'code' => strtoupper($code),
                'name' => strtolower($name)
            ]);

            return true;

        }
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function update(Request $request): bool
    {
        if ($country = $this->country->where('code', '=', $request->input('code'))->get()->first()) {

            $country->update(['name' => $request->input('name')]);
            return true;

        } else {
            $this->create($request);
        }
    }

    /**
     * @param Country $country
     * @return bool
     * @throws Exception
     */
    public function delete(Country $country): bool
    {
        return $country->delete();
    }

    /**
     * @param string $id
     * @return Country|null
     */
    public function existsThisCountry(string $id): Country | bool
    {
        $country = $this->country->find($id);

        return !is_null($country) ? $country : false;
    }

    /**
     * @param Country $country
     * @return bool
     */
    public function hasThisCountryAnyCityRelated(Country $country): bool
    {
        return count($country->cities) > 0;
    }

    /**
     * @return array
     */
    public function getAllCountries(): array
    {
        $countries = $this->country->all();

        return !is_null($countries) ? $countries->toArray() : [];
    }

    /**
     * @param string $id
     * @return array
     */
    public function getCountryById(string $id): array
    {
        $country = $this->country->find($id);

        return !is_null($country) ? $country->toArray() : [];
    }
}
