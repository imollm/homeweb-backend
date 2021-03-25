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
     * @var Property
     */
    private Property $property;

    /**
     * @var User
     */
    private User $user;

    /**
     * @var RangePrice
     */
    private RangePrice $rangePrice;

    /**
     * PropertyService constructor.
     * @param Property $property
     * @param User $user
     * @param RangePrice $rangePrice
     */
    public function __construct(Property $property, User $user, RangePrice $rangePrice)
    {
        $this->property = $property;
        $this->user = $user;
        $this->rangePrice = $rangePrice;
    }

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

            return $this->property->find($propertyId)->update($request->all()) ? true : false;

        } elseif ($action === 'create') {

            $ownerId = $request->input('user_id');

            if (is_numeric($ownerId)) {
                if ($this->haveThisUserOwnerRole($ownerId)) {
                    return $this->property->create($request->all()) ? true : false;
                } else {
                    return false;
                }
            } else {
                return $this->property->create($request->all()) ? true : false;
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
        return $this->user->find($userId)->role->name === 'owner';
    }

    /**
     * @param Request $request
     * @throws ValidationException
     */
    public function validateFilterPostData(Request $request)
    {
        $data = [
            'reference' => $request->reference,
            'price' => $request->price,
            'location' => $request->location,
            'category' => $request->category,
        ];

        Validator::make($data, [
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
            'reference' => $request->reference,
            'price' => $request->price,
            'city_id' => $request->location,
            'category_id' => $request->category,
        ];

        if (!empty($conditions['reference']) || !empty($conditions['price']) || !empty($conditions['city_id']) || !empty($conditions['category_id'])) {

            $query = $this->property->select('*');

            foreach ($conditions as $condition => $value) {

                if (!is_null($value)) {

                    if ($condition === 'price') {

                        $query->where(function ($q) use ($value) {

                            $bigPrice = $this->rangePrice->where('id', $value)->first()->big_price;
                            $smallPrice = $this->rangePrice->where('id', $value)->first()->small_price;

                            $q->whereBetween('price', [$smallPrice, $bigPrice]);

                        });
                    } else {
                        $query->where($condition, $value);
                    }
                }
            }
            return $query->get();
        } else {
            return null;
        }
    }

    /**
     * Return if property exists or not exists
     *
     * @param string $id
     * @return bool
     */
    public function existsThisProperty(string $id): bool
    {
        return !is_null($this->property->find($id));
    }

    /**
     * Return the property owner id
     *
     * @param string $id
     * @return int
     */
    public function whichIsTheOwnerIdOfThisProperty(string $id): int
    {
        return $this->property->find($id)->owner->id;
    }

    /**
     * @param string $id
     * @return array
     */
    public function getPriceHistoryOfThisProperty(string $id): array
    {
        return $this->property->find($id)->priceHistory->toArray();
    }

    /**
     * @param string $propertyId
     * @param float $price
     * @return bool
     */
    public function updatePriceByPropertyId(string $propertyId, float $price): bool
    {
        return $this->property->whereId($propertyId)->update(['price' => $price]);
    }


    /**
     * @return array
     */
    public function getActiveProperties(): array
    {
        $activeProperties = $this->property->whereActive(true)->get()->toArray();

        return !is_null($activeProperties) ? $activeProperties : [];
    }

    /**
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool
    {
        if ($this->canBeDeletedThisProperty($id)) {

            if ($this->property->find($id)->delete()) {

                return true;

            }

        }
        return false;
    }

    /**
     * @param string $id
     * @return bool
     */
    private function canBeDeletedThisProperty(string $id): bool
    {
        $property = $this->property->find($id);

        return
            count($property->sales) === 0 ||
            count($property->tours) === 0;
    }

    /**
     * @param string $id
     * @return Property
     */
    public function getPropertyById(string $id): Property
    {
        return $this->property->find($id);
    }
}
