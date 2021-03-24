<?php


namespace App\Services\Sale;


use App\Models\Property;
use App\Models\Sale;
use App\Models\User;
use App\Services\Property\PropertyService;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class SaleService
 * @package App\Services\Sale
 */
class SaleService implements ISaleService
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
     * @var Sale
     */
    private Sale $sale;

    /**
     * @var Property
     */
    private Property $property;

    private User $user;

    /**
     * SaleService constructor.
     * @param PropertyService $propertyService
     * @param UserService $userService
     * @param Sale $sale
     * @param Property $property
     * @param User $user
     */
    public function __construct(PropertyService $propertyService, UserService $userService, Sale $sale, Property $property, User $user)
    {
        $this->propertyService = $propertyService;
        $this->userService = $userService;
        $this->sale = $sale;
        $this->property = $property;
        $this->user = $user;
    }

    /**
     * @param Request $request
     * @throws ValidationException
     */
    public function validatePostData(Request $request)
    {
        Validator::make($request->all(), [
            'property_id' => 'required|numeric',
            'buyer_id' => 'required|numeric',
            'seller_id' => 'required|numeric',
            'date' => 'required|date_format:Y-m-d',
            'amount' => 'required|numeric'
        ])->validate();
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function allDataExists(Request $request): bool
    {
        $propertyId = $request->input('property_id');
        $buyerId = $request->input('buyer_id');
        $sellerId = $request->input('seller_id');

        return
            $this->propertyService->existsThisProperty($propertyId) &&
            $this->userService->existsThisCustomer($buyerId) &&
            $this->userService->existsThisEmployee($sellerId);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function create(Request $request): bool
    {
        $propertyId = $request->input('property_id');
        $buyerId = $request->input('buyer_id');
        $sellerId = $request->input('seller_id');
        $date = $request->input('date');
        $amount = $request->input('amount');

        $hashId = hash("sha256", $propertyId.$buyerId.$sellerId.$date);

        $sale = $this->sale->create([
            'property_id' => $propertyId,
            'buyer_id' => $buyerId,
            'seller_id' => $sellerId,
            'date' => $date,
            'amount' => $amount,
            'hash_id' => $hashId
        ]);

        // Update bool sale on property table

        $sold = $this->property->whereId($propertyId)->update(['sold' => true]);

        return $sale && $sold;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function isToSellThisProperty(Request $request): bool
    {
        $propertyId = $request->input('property_id');

        $sold = $this->property->find($propertyId)->sold;

        return !$sold;
    }

    /**
     * @return array
     */
    public function getAllSales(): array
    {
        return $this->sale->all()->toArray();
    }

    /**
     * @param string $ownerId
     * @return array
     */
    public function getSalesByOwnerId(string $ownerId): array
    {
        if ($this->userService->existsThisOwner($ownerId)) {

            $myPropertiesIds = $this->property->whereUserId($ownerId)->get()->pluck('id');

            return $this->sale->wherePropertyId($myPropertiesIds)->get()->toArray();

        }
        return array();
    }

    /**
     * @param string $customerId
     * @return array
     */
    public function getSalesByCustomerId(string $customerId): array
    {
        if ($this->userService->existsThisCustomer($customerId)) {

            return $this->sale->whereBuyerId($customerId)->get()->toArray();

        }
        return array();
    }

    /**
     * @param string $employeeId
     * @return array
     */
    public function getSalesByEmployeeId(string $employeeId): array
    {
        if ($this->userService->existsThisEmployee($employeeId)) {

            return $this->sale->whereSellerId($employeeId)->get()->toArray();

        }
        return array();
    }
}
