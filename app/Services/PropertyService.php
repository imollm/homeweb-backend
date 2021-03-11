<?php


namespace App\Services;

use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class PropertyService
 * @package App\Services
 */
class PropertyService implements PropertyServiceI
{
    /**
     * Validate request data and return new property with this data
     *
     * @param Request $request
     * @throws ValidationException
     */
    public function validatePostPropertyData(Request $request)
    {
            Validator::make($request->all(), [
                'category_id' => 'required|numeric',
                'user_id' => 'numeric|nullable',
                'title' => 'required|string|max:255',
                'reference' => 'required|unique:properties|max:255',
            ])->validate();

//        $property = new Property();
//
//        $property->user_id = $request->input('user_id');
//        $property->category_id = $request->input('category_id');
//        $property->title = $request->input('title');
//        $property->reference = $request->input('reference');
//        $property->plot_meters = $request->input('plot_meters');
//        $property->built_meters = $request->input('built_meters');
//        $property->address = $request->input('address');
//        $property->location = json_encode(["longitude" => (float)$request->input('longitude'), "latitude" => (float)$request->input('latitude')], JSON_FORCE_OBJECT);
//        $property->description = $request->input('description');
//        $property->energetic_certification = $request->input('energetic_certification');
//        $property->price = $request->input('price');
//        $property->active = $request->input('active');
//        $property->sold = $request->input('sold');
//
//        return $property;
    }

    /**
     * Method to save a new property
     *
     * @param Request $request
     * @param string $action
     * @param string $propertyId
     * @return bool
     */
    public function createOrUpdateProperty(Request $request, string $action, string $propertyId  = ''): bool
    {
        $saved = false;

        $role = $this->whichRoleWantsToCreateOrUpdateProperty();

        if ($role === 'owner') {
            $saved = $this->roleOwnerWantsCreateOrUpdateProperty($request, $action);
        }
        elseif ($role === 'admin' || $role === 'employee') {
            $saved = $this->roleAdminOrEmployeeWantsCreateOrUpdateProperty($request, $action, $propertyId);
        }
         return $saved;
    }

    /**
     * Determine which role wants request create property
     *
     * @return string
     */
    private function whichRoleWantsToCreateOrUpdateProperty(): string
    {
        return Auth::user()->role->name;
    }

    /**
     * When owner wants to save new property, on her properties
     *
     * @param Request $request
     * @param string $action
     * @return bool
     */
    private function roleOwnerWantsCreateOrUpdateProperty(Request $request, string $action): bool
    {
        $property = new Property($request->all());
        if ($action === 'update') {

            return Auth::user()->properties()->updateOrCreate($request->all()) ? true : false;

        } elseif ($action === 'create') {

            return Auth::user()->properties()->save($property) ? true : false;
        }
    }

    /**
     * When admin or employee wants save new property
     *
     * @param Request $request
     * @param string $action
     * @param string $propertyId
     * @return bool
     */
    private function roleAdminOrEmployeeWantsCreateOrUpdateProperty(Request $request, string $action, string $propertyId): bool
    {
        if ($action === 'update') {

            return Property::find($propertyId)->update($request->all()) ? true : false;

        } elseif ($action === 'create' &&
            $this->haveThisUserOwnerRole($request->input('user_id'))) {

            return Property::create($request->all()) ? true : false;

        } else {
            return false;
        }
    }

    private function haveThisUserOwnerRole(string $userId): bool
    {
        return User::find($userId)->role->name === 'owner';
    }
}
