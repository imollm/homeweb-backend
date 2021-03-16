<?php


namespace App\Services\Property;

use App\Models\Property;
use App\Models\RangePrice;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class PropertyService
 * @package App\Services
 */
class PropertyService implements IPropertyService
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
                'reference' => 'required|string|unique:properties|max:255',
            ])->validate();
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

            return Auth::user()->properties()->update($request->all()) ? true : false;

        } elseif ($action === 'create') {

            return Auth::user()->properties()->save($property) ? true : false;
        }
        return false;
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

        } elseif ($action === 'create') {

            $ownerId = $request->input('user_id');

            if (is_numeric($ownerId)) {
                if ($this->haveThisUserOwnerRole($ownerId)) {
                    return Property::create($request->all()) ? true : false;
                } else {
                    return false;
                }
            } else {
                return Property::create($request->all()) ? true : false;
            }
        } else {
            return false;
        }
    }

    /**
     * @param string $userId
     * @return bool
     */
    private function haveThisUserOwnerRole(string $userId): bool
    {
        return User::find($userId)->role->name === 'owner';
    }

    /**
     * @param Request $request
     * @throws ValidationException
     */
    public function validateFilterPostData(Request $request)
    {
        Validator::make($request->all(), [
            'reference' => 'nullable|max:255',
            'price' => 'nullable',
            'location' => 'nullable',
            'category' => 'nullable',
        ])->validate();
    }

    /**
     * @param Request $request
     * @return Property|null
     */
    public function getPropertiesByFilters(Request $request): Collection | null
    {
        $conditions = [
            'reference' => $request->has('reference') ? $request->input('reference') : '',
            'price' => $request->has('price') ? $request->input('price') : '',
            'city_id' => $request->has('city_id') ? $request->input('city_id') : '',
            'category_id' => $request->has('category_id') ? $request->input('category_id') : '',
        ];

        $query = Property::select();

        foreach ($conditions as $condition => $value) {

            if (!is_null($value)) {

                if ($condition === 'price') {

                    $query->where(function ($q) use ($value) {

                        $bigPrice = RangePrice::where('id', $value)->first()->big_price;
                        $smallPrice = RangePrice::where('id', $value)->first()->small_price;

                        $q->whereBetween('price', [$smallPrice, $bigPrice]);

                    });
                } else {
                    $query->where($condition, $value);
                }
            }
        }
        return $query->get();
    }

    /**
     * Return if property exists or not exists
     *
     * @param string $id
     * @return bool
     */
    public function existsThisProperty(string $id): bool
    {
        return !is_null(Property::find($id));
    }

    /**
     * Return the property owner id
     *
     * @param string $id
     * @return int
     */
    public function whichIsTheOwnerIdOfThisProperty(string $id): int
    {
        return Property::find($id)->owner->id;
    }

    /**
     * @param string $id
     * @return array
     */
    public function getPriceHistoryOfThisProperty(string $id): array
    {
        return Property::find($id)->priceHistory->toArray();
    }
}