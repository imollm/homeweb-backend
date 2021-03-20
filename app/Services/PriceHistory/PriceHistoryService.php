<?php


namespace App\Services\PriceHistory;

use App\Models\PriceHistory;
use App\Models\Property;
use App\Services\Property\PropertyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class PriceHistoryService
 * @package App\Services\PriceHistory
 */
class PriceHistoryService implements IPriceHistory
{
    /**
     * @var PropertyService
     */
    private PropertyService $propertyService;

    /**
     * PriceHistoryService constructor.
     * @param PropertyService $propertyService
     */
    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }

    /**
     * @param Request $request
     * @throws ValidationException
     */
    public function validatePostData(Request $request)
    {
        Validator::make($request->all(), [
            'property_id' => 'required|numeric',
            'start' => 'required|date_format:Y-m-d',
            'amount' => 'required|numeric',
            'end' => 'nullable|date_format:Y-m-d'
        ])->validate();
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function hasThisPropertyTheSamePriceChange(Request $request): bool
    {
        $propertyId = $request->input('property_id');
        $startTimestamp = $request->input('start');
        $amount = $request->input('amount');

        $hasAlreadyThisChange =
            PriceHistory::where([
                'property_id' => $propertyId,
                'start' => $startTimestamp,
                'amount' => $amount
            ])->get()->first();

        return !is_null($hasAlreadyThisChange) ? false : true;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function startTimestampGivenIsGreaterThanLast(Request $request): bool
    {
        $propertyId = $request->input('property_id');
        $startTimestampGiven = $request->input('start');

        $lastPriceChange =
            PriceHistory::wherePropertyId($propertyId)
                            ->whereEnd(null)
                            ->get()
                            ->first();

        return $lastPriceChange->start < $startTimestampGiven;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function create(Request $request): bool
    {
        $propertyId = $request->input('property_id');
        $startTimestamp = $request->input('start');
        $amount = $request->input('amount');

        // First update end_timestamp of last price change

        PriceHistory::wherePropertyId($propertyId)
                            ->whereEnd(null)
                            ->update([
                                'end' => $startTimestamp
                            ]);

        // Second create new price change

        $newPriceChange = PriceHistory::create([
            'property_id' => $propertyId,
            'start' => $startTimestamp,
            'amount' => $amount,
            'end' => null
        ]);

        // Third update price of property

        $updatedPriceOnProperty = $this->propertyService->updatePriceByPropertyId($propertyId, $amount);

        return !is_null($newPriceChange) && $updatedPriceOnProperty;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function areYouAllowedToStoreAPriceChangeOfThisProperty(Request $request): bool
    {
        $propertyId = $request->input('property_id');

        $ownerId = Property::find($propertyId)->owner->id;

        return Auth::user()->role->name === 'owner' && $ownerId === Auth::user()->id;
    }
}
