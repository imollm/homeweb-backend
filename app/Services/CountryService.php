<?php


namespace App\Services;


use App\Models\Country;
use Illuminate\Http\Request;
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
            'code' => 'required|unique:countries|max:3',
            'name' => 'required|max:255'
        ])->validate();
    }

    public function create(Request $request): bool
    {
        $code = $request->input('code');
        $name = $request->input('name');

        if ($this->thisCountryExists($code, $name)) {

            return false;

        } else {

            $country = Country::create([
                'code' => strtoupper($code),
                'name' => strtolower($name)
            ]);

            return is_numeric($country->id);
        }
    }

    private function thisCountryExists(string $code, string $name): bool
    {
        $result = Country::select('*')
            ->where('code', '=', strtoupper($code))
            ->orWhere('name', '=', strtolower($name))
            ->get()
            ->first();

        return !is_null($result);
    }
}
