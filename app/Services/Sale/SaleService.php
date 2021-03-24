<?php


namespace App\Services\Sale;


use App\Models\Property;
use App\Models\Sale;
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
     * SaleService constructor.
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

        $sale = Sale::create([
            'property_id' => $propertyId,
            'buyer_id' => $buyerId,
            'seller_id' => $sellerId,
            'date' => $date,
            'amount' => $amount,
            'hash_id' => $hashId
        ]);

        // Update bool sale on property table

        $sold = Property::whereId($propertyId)->update(['sold' => true]);

        return $sale && $sold;
    }

    public function isToSellThisProperty(Request $request): bool
    {
        $propertyId = $request->input('property_id');

        $sold = Property::find($propertyId)->sold;

        return !$sold;
    }
}
