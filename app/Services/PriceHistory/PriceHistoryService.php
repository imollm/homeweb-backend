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
     * @var PriceHistory
     */
    private PriceHistory $priceHistory;

    /**
     * @var PropertyService
     */
    private PropertyService $propertyService;

    /**
     * PriceHistoryService constructor.
     * @param PriceHistory $priceHistory
     * @param PropertyService $propertyService
     */
    public function __construct(PriceHistory $priceHistory, PropertyService $propertyService)
    {
        $this->priceHistory = $priceHistory;
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
            $this->priceHistory->where([
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
            $this->priceHistory->wherePropertyId($propertyId)
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

        $this->priceHistory->wherePropertyId($propertyId)
                            ->whereEnd(null)
                            ->update([
                                'end' => $startTimestamp
                            ]);

        // Second create new price change

        $newPriceChange = $this->priceHistory->create([
            'hash_id' => hash("sha256", $propertyId.$startTimestamp.$amount),
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

        $property = $this->propertyService->getPropertyById($propertyId);

        if (is_null($property)) return false;

        $ownerId = $property->owner->id;

        return Auth::user()->role->name === 'owner' && $ownerId === Auth::user()->id;
    }

    /**
     * @return array
     */
    public function getAllChanges(): array
    {
        return $this->priceHistory->with('property')->get()->toArray();
    }

    /**
     * @return array
     */
    public function getPriceChangesOfPropertiesOwnedByAuthOwner(): array
    {
        $data = [];
        $propertiesIDsOfAuthOwner = Auth::user()->properties()->get()->pluck('id')->toArray();
        $data['changes'] = PriceHistory::whereIn('property_id', $propertiesIDsOfAuthOwner)->with('property')->get()->toArray();
        $data['chart'] = $this->setDataToPriceChangesChart($data['changes']);

        return $data;
    }

    private function setDataToPriceChangesChart(array $changes): array
    {
        $data = [];
        $flag = false;
        if (count($changes) > 0) {
            foreach ($changes as $change) {
                foreach ($data as $index => $property) {
                    if ($property['property'] === $change['property_id']) {
                        array_push($data[$index]['changes'], ['price' => $change['amount'], 'day' => $change['start']]);
                        $flag = true;
                    }
                }
                if ($flag === false) {
                    array_push($data, ['property' => $change['property_id'], 'reference' => $change['property']['reference'],'changes' => [array('price' => $change['amount'], 'day' => $change['start'])]]);
                }
                $flag = false;
            }
        }
        return $data;
    }
}
