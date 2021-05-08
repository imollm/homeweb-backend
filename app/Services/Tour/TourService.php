<?php


namespace App\Services\Tour;


use App\Models\Tour;
use App\Services\Property\PropertyService;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class TourService
 * @package App\Services\Tour
 */
class TourService implements ITourService
{
    /**
     * @var PropertyService
     */
    private PropertyService $propertyService;

    /**
     * @var UserService
     */
    private UserService $userService;

    /**
     * TourService constructor.
     * @param PropertyService $propertyService
     * @param UserService $userService
     */
    public function __construct(PropertyService $propertyService, UserService $userService)
    {
        $this->propertyService = $propertyService;
        $this->userService = $userService;
    }

    /**
     * @param Request $request
     * @throws ValidationException
     */
    public function validatePostData(Request $request)
    {
        Validator::make($request->all(), [
            'property_id' => 'required|numeric',
            'customer_id' => 'required|numeric',
            'employee_id' => 'required|numeric',
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i:s'
        ])->validate();
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function allDataExists(Request $request): bool
    {
        $propertyId = $request->input('property_id');
        $customerId = $request->input('customer_id');
        $employeeId = $request->input('employee_id');

        return
            $this->propertyService->existsThisProperty($propertyId) &&
            $this->userService->existsThisCustomer($customerId) &&
            $this->userService->existsThisEmployee($employeeId);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function areAvailability(Request $request): bool
    {
        $propertyId = $request->input('property_id');
        $customerId = $request->input('customer_id');
        $employeeId = $request->input('employee_id');
        $date = $request->input('date');
        $time = $request->input('time');

        $propertyAvailability = Tour::wherePropertyId($propertyId)->where('date', '=', $date)->where('time', '=', $time)->get()->first();
        $customerAvailability = Tour::whereCustomerId($customerId)->where('date', '=', $date)->where('time', '=', $time)->get()->first();
        $employeeAvailability = Tour::whereEmployeeId($employeeId)->where('date', '=', $date)->where('time', '=', $time)->get()->first();

        return is_null($propertyAvailability) && is_null($customerAvailability) && is_null($employeeAvailability);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function create(Request $request): bool
    {
        $propertyId = $request->input('property_id');
        $customerId = $request->input('customer_id');
        $employeeId = $request->input('employee_id');
        $date = $request->input('date');
        $time = $request->input('time');

        $tour = Tour::create([
            'property_id' => $propertyId,
            'customer_id' => $customerId,
            'employee_id' => $employeeId,
            'date' => $date,
            'time' => $time,
            'hash_id' => hash('sha256', $propertyId.$customerId.$employeeId.$date.$time)
        ]);

        return !is_null($tour);
    }

    /**
     * @param string $hashId
     * @return array
     */
    public function getTourByHashId(string $hashId): array
    {
        return Tour::whereHashId($hashId)
                    ->with('property')
                    ->with('customer')
                    ->with('employee')
                    ->get()->first()->toArray();
    }

    /**
     * @param string $propertyId
     * @return array
     */
    public function getToursByPropertyId(string $propertyId): array
    {
        return Tour::wherePropertyId($propertyId)->get()->toArray();
    }

    /**
     * @param string $propertyId
     * @param string $customerId
     * @return bool
     */
    public function haveThisCustomerToursWithThisPropertyId(string $propertyId, string $customerId): bool
    {
        $haveATour = Tour::wherePropertyId($propertyId)->whereCustomerId($customerId)->get();

        return count($haveATour) > 0;
    }

    /**
     * @param string $customerId
     * @param string $propertyId
     * @return array
     */
    public function getToursByCustomerIdAndPropertyId(string $customerId, string $propertyId): array
    {
        return Tour::whereCustomerId($customerId)->wherePropertyId($propertyId)->get()->toArray();
    }

    /**
     * @param string $propertyId
     * @param string $employeeId
     * @return bool
     */
    public function haveThisEmployeeToursWithThisPropertyId(string $propertyId, string $employeeId): bool
    {
        $haveATour = Tour::wherePropertyId($propertyId)->whereEmployeeId($employeeId)->get();

        return count($haveATour) > 0;
    }

    /**
     * @param string $employeeId
     * @param string $propertyId
     * @return array
     */
    public function getToursByEmployeeIdAndPropertyId(string $employeeId, string $propertyId): array
    {
        return Tour::whereEmployeeId($employeeId)->wherePropertyId($propertyId)->get()->toArray();
    }

    /**
     * @return array
     */
    public function getAllTours(): array
    {
        return Tour::all()->toArray();
    }

    /**
     * @param int $limit
     * @return array
     */
    public function getLastTours(int $limit = 3): array
    {
        return Tour::with('property')
                    ->with('employee')
                    ->with('customer')
                    ->orderBy('created_at', 'desc')
                    ->take($limit)->get()->toArray();
    }

    /**
     * @return bool
     */
    public function areToursIntoSystem(): bool
    {
        return Tour::count() > 0;
    }

    /**
     * @param string $customerId
     * @return bool
     */
    public function haveThisCustomerTours(string $customerId): bool
    {
        return Tour::whereCustomerId($customerId)->count() > 0;
    }

    /**
     * @param string $customerId
     * @return array
     */
    public function getToursByCustomerId(string $customerId): array
    {
        return Tour::whereCustomerId($customerId)->with('property')->with('employee')->orderBy('created_at', 'desc')->get()->toArray();
    }

    /**
     * @param string $employeeId
     * @return bool
     */
    public function haveThisEmployeeTours(string $employeeId): bool
    {
        return Tour::whereEmployeeId($employeeId)->count() > 0;
    }

    /**
     * @param string $employeeId
     * @return array
     */
    public function getToursByEmployeeId(string $employeeId): array
    {
        return Tour::whereEmployeeId($employeeId)
                    ->orderBy('created_at', 'desc')
                    ->with('property')
                    ->with('employee')
                    ->with('customer')
                    ->get()->toArray();
    }

    /**
     * @param string $ownerId
     * @return bool
     */
    public function haveThisOwnerPropertiesWithTours(string $ownerId): bool
    {
        return Tour::join('properties', 'properties.id', '=', 'tours.property_id')
            ->where('properties.user_id', '=', $ownerId)
            ->orderBy('created_at', 'desc')
            ->count() > 0;
    }

    /**
     * @param string $ownerId
     * @return array
     */
    public function getToursOfPropertiesOwnedByOwnerId(string $ownerId): array
    {
        return Tour::join('properties', 'properties.id', '=', 'tours.property_id')
            ->where('properties.user_id', '=', $ownerId)
            ->orderBy('created_at', 'desc')
            ->get('tours.*')
            ->toArray();
    }

    /**
     * @param string $role
     * @param string $userId
     * @param string $hashId
     * @return bool
     */
    public function thisUserIsRelatedWithThisTour(string $role, string $userId, string $hashId): bool
    {
        if ($role === 'admin') {
            return true;
        } else if ($role === 'employee') {
            return Tour::whereEmployeeId($userId)->whereHashId($hashId)->count() > 0;
        } else if ($role === 'customer') {
            return Tour::whereCustomerId($userId)->whereHashId($hashId)->count() > 0;
        } else {
            return false;
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     */
    public function validateHashId(Request $request)
    {
        Validator::make($request->all(), [
            'hash_id' => 'required|string'
        ])->validate();
    }

    /**
     * @param string $hashId
     * @return bool
     */
    public function existsThisTourByHashId(string $hashId): bool
    {
        return Tour::whereHashId($hashId)->count() > 0;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function update(Request $request): bool
    {
        $propertyId = $request->input('property_id');
        $customerId = $request->input('customer_id');
        $employeeId = $request->input('employee_id');
        $date = $request->input('date');
        $time = $request->input('time');
        $hashId = $request->input('hash_id');

        $newHashId = hash("sha256", $propertyId.$customerId.$employeeId.$date.$time);

        return Tour::whereHashId($hashId)->update([
            'date' => $date,
            'time' => $time,
            'hash_id' => $newHashId
        ]);
    }

    /**
     * @param string $hashId
     * @return bool
     */
    public function delete(string $hashId): bool
    {
        $tourDeleted = Tour::whereHashId($hashId)->delete();

        return !is_null($tourDeleted);
    }
}
