<?php


namespace App\Services\Property;

use App\Models\Property;
use App\Models\RangePrice;
use App\Models\User;
use App\Services\Feature\FeatureService;
use App\Services\File\FileService;
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
     * @var FileService
     */
    private FileService $fileService;

    /**
     * @var FeatureService
     */
    private FeatureService $featureService;

    /**
     * PropertyService constructor.
     * @param Property $property
     * @param User $user
     * @param RangePrice $rangePrice
     * @param FileService $fileService
     * @param FeatureService $featureService
     */
    public function __construct(Property $property, User $user, RangePrice $rangePrice, FileService $fileService, FeatureService $featureService)
    {
        $this->property = $property;
        $this->user = $user;
        $this->rangePrice = $rangePrice;
        $this->fileService = $fileService;
        $this->featureService = $featureService;
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
                'city_id' => 'required|numeric',
                'category_id' => 'required|numeric',
                'user_id' => 'numeric|nullable',
                'title' => 'required|string|max:255',
                'reference' => 'required|string|unique:properties|max:255',
            ])->validate();
    }

    /**
     * @param Request $request
     * @throws ValidationException
     */
    public function validatePutPropertyData(Request $request)
    {
        Validator::make($request->all(), [
            'category_id' => 'required|numeric',
            'user_id' => 'numeric|nullable',
            'title' => 'required|string|max:255',
        ])->validate();
    }

    /**
     * Method to save a new property
     *
     * @param Request $request
     * @param string $action
     * @param string $propertyId
     * @return bool
     * @throws ValidationException
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
     * @throws ValidationException
     */
    private function roleOwnerWantsCreateOrUpdateProperty(Request $request, string $action): bool
    {
        $saved = false;

        if ($action === 'update') {

            $saved = Auth::user()->properties()->update($request->except(['id', 'reference', 'features'])) ? true : false;

            $propertyId = $request->input('id');

            if ($request->has('features') && count($request->input('features')) > 0) {
                $this->property->find($propertyId)->features()->sync($this->featureService->setFeaturesToBeSaved($request));
            }

        } elseif ($action === 'create') {

            $property = new Property($request->all());
            $saved = Auth::user()->properties()->save($property);

            if ($request->has('features') && count($request->input('features')) > 0) {
                $saved->features()->attach($this->featureService->setFeaturesToBeSaved($request));
            }

        }

        if ($request->has('image') && $request->hasFile('image')) $this->fileService->storePropertyImage($request);

        return $saved || is_object($saved);
    }

    /**
     * When admin or employee wants save new property
     *
     * @param Request $request
     * @param string $action
     * @param string $propertyId
     * @return bool
     * @throws ValidationException
     */
    private function roleAdminOrEmployeeWantsCreateOrUpdateProperty(Request $request, string $action, string $propertyId): bool
    {
        $saved = false;

        if ($action === 'update') {

            $saved = $this->property->find($propertyId)->update($request->all());

            if ($request->has('features') && count($request->input('features')) > 0) {
                $this->property->find($propertyId)->features()->sync($this->featureService->setFeaturesToBeSaved($request));
            }

        } elseif ($action === 'create') {

            $ownerId = $request->input('user_id');

            if ((is_numeric($ownerId) && $this->haveThisUserOwnerRole($ownerId) || empty($ownerId))) {
                $saved = $this->property->create($request->all());

                if ($request->has('features') && count($request->input('features')) > 0) {
                    $saved->features()->attach($this->featureService->setFeaturesToBeSaved($request));
                }
            }
        }

        if ($request->has('image') && $request->hasFile('image')) $this->fileService->storePropertyImage($request);

        return $saved !== false;
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
     * @return Property|false
     */
    public function getPropertyById(string $id): Property | false
    {
        $property = $this->property->whereId($id)
                            ->with('city')
                            ->with('owner')
                            ->with('category')
                            ->with('features')
                            ->with('sales')
                            ->get()
                            ->first();

        return !is_null($property) ? $property : false ;
    }

    /**
     * @param int $count
     * @return array
     */
    public function getLastProperties(int $count): array
    {
        return $this->property
                ->with('city')
                ->with('category')
                ->orderBy('created_at', 'desc')
                ->take($count)
                ->get()
                ->toArray();
    }

    /**
     * @return array
     */
    public function getAllProperties(): array
    {
        return $this->property->all()->toArray();
    }

    /**
     * @param $count
     * @return array
     */
    public function getLastActiveProperties($count): array
    {
        return $this->property->whereActive(true)->orderBy('created_at', 'desc')->take($count)->get()->toArray();
    }

    /**
     * @return array
     */
    public function getForSaleProperties(): array
    {
        return $this->property->whereSold(false)->get()->toArray();
    }
}
