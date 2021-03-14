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

        if (!is_null(Country::where('code', '=', $code)->get()->first())) {

            return false;

        } else {

            Country::create([
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
        if ($country = Country::where('code', '=', $request->input('code'))->get()->first()) {

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
    public function existsThisCountry(string $id): Country | null
    {
        return Country::find($id);
    }

    /**
     * @param Country $country
     * @return bool
     */
    public function hasThisCountryAnyCityRelated(Country $country): bool
    {
        return count($country->cities) > 0;
    }
}
