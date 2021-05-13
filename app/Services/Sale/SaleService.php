<?php


namespace App\Services\Sale;


use App\Models\Property;
use App\Models\Sale;
use App\Models\User;
use App\Services\Property\PropertyService;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    /**
     * @var User
     */
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
     * @param int $limit
     * @return array
     */
    public function getLastSales(int $limit = 3): array
    {
        $from = date('Y-m-d', strtotime('-1 month'));
        $to = date('Y-m-d', strtotime('+1 month'));

        return [
            'last' => $this->sale->with('property')->with('buyer')->with('seller')->orderBy('date', 'desc')->take($limit)->get()->toArray(),
            'sales' => $this->sale->count(),
            'amount' => $this->sale->sum('amount'),
            'month' => $this->sale->whereBetween('date', array($from, $to))->sum('amount')
        ];
    }

    /**
     * @param string $ownerId
     * @return array
     */
    public function getSalesByOwnerId(string $ownerId): array
    {
        if ($this->userService->existsThisOwner($ownerId)) {

            $myPropertiesIds = $this->property->whereUserId($ownerId)->get()->pluck('id');

            return $this->sale->whereIn('property_id', $myPropertiesIds)->get()->toArray();

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

            return $this->sale->whereBuyerId($customerId)->with('property')->with('seller')->get()->toArray();

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

            return $this->sale->whereSellerId($employeeId)->with('property')->with('buyer')->get()->toArray();

        }
        return array();
    }

    /**
     * @param string $hashId
     * @param string $role
     * @param string $userId
     * @return array
     */
    public function getSaleByHashId(string $hashId, string $role, string $userId): array
    {
        return match($role) {
            'admin' => $this->getSale($hashId),
            'customer' => $this->isThisSaleOfThisCustomer($userId, $hashId),
            'employee' => $this->isThisSaleOfThisEmployee($userId, $hashId),
            'owner' => $this->isThisSaleOfThisOwner($userId, $hashId),
            default => []
        };
    }

    /**
     * @param string $userId
     * @param string $hashId
     * @return array
     */
    private function isThisSaleOfThisCustomer(string $userId, string $hashId): array
    {
        $buyerId = $this->sale->whereHashId($hashId)->get()->first()->buyer_id;

        return $buyerId == $userId ? $this->getSale($hashId) : [];
    }

    /**
     * @param string $userId
     * @param string $hashId
     * @return array
     */
    private function isThisSaleOfThisEmployee(string $userId, string $hashId): array
    {
        $sellerId = $this->sale->whereHashId($hashId)->get()->first()->seller_id;

        return $sellerId == $userId ? $this->getSale($hashId) : [];
    }

    /**
     * @param string $userId
     * @param string $hashId
     * @return array
     */
    private function isThisSaleOfThisOwner(string $userId, string $hashId): array
    {
        $propertyId = $this->sale->whereHashId($hashId)->get()->first()->property_id;

        $is = $this->property->whereUserId($userId)->whereId($propertyId)->get()->first();

        return !is_null($is) ? $this->getSale($hashId) : [];
    }

    /**
     * @param string $hashId
     * @return mixed
     */
    private function getSale(string $hashId): array
    {
        $sale = $this->sale->whereHashId($hashId)->with('buyer')->with('seller')->with('property')->get()->first();

        return !is_null($sale) ? $sale->toArray() : [];
    }

    /**
     * @param string $year
     * @return array
     */
    public function getSalesOfActualYear(string $year): array
    {
        $sales = [
            ['month' => 'January', 'amount' => 0],
            ['month' => 'February', 'amount' => 0],
            ['month' => 'May', 'amount' => 0],
            ['month' => 'April', 'amount' => 0],
            ['month' => 'March', 'amount' => 0],
            ['month' => 'June', 'amount' => 0],
            ['month' => 'July', 'amount' => 0],
            ['month' => 'August', 'amount' => 0],
            ['month' => 'September', 'amount' => 0],
            ['month' => 'October', 'amount' => 0],
            ['month' => 'November', 'amount' => 0],
            ['month' => 'December', 'amount' => 0]
        ];

        $results = DB::select("SELECT MONTHNAME(date) AS 'month_name', SUM(amount) AS 'amount' FROM sales WHERE YEAR(date) = ? GROUP BY MONTHNAME(date)", [$year]);

        if (count($results) > 0) {
            foreach ($sales as $index => $sale) {
                foreach ($results as $result) {
                    if (strtolower($result->month_name) === strtolower($sale['month'])) {
                        $sales[$index]['amount'] = $result->amount;
                        break;
                    }
                }
            }
        }

        return $sales;
    }

    /**
     * @return array
     */
    public function getSalesByCategories(): array
    {
        return DB::table('sales')
                    ->selectRaw('categories.name AS category, SUM(sales.amount) AS amount')
                    ->join('properties', 'sales.property_id', '=', 'properties.id')
                    ->join('categories', 'properties.category_id', '=', 'categories.id')
                    ->groupBy('categories.name')->get()->toArray();
    }

    /**
     * @return array
     */
    public function getSalesByCountries(): array
    {
        return DB::table('sales')
                    ->selectRaw('countries.name AS country, SUM(sales.amount) AS amount')
                    ->join('properties', 'sales.property_id', '=', 'properties.id')
                    ->join('cities', 'properties.city_id', '=', 'cities.id')
                    ->join('countries', 'cities.country_id', '=', 'countries.id')
                    ->groupBy('countries.name')->get()->toArray();
    }

    /**
     * @return array
     */
    public function getSalesByCities(): array
    {
        return DB::table('sales')
                    ->selectRaw('cities.name AS city, SUM(sales.amount) AS amount')
                    ->join('properties', 'sales.property_id', '=', 'properties.id')
                    ->join('cities', 'properties.city_id', '=', 'cities.id')
                    ->groupBy('cities.name')->get()->toArray();
    }

    /**
     * @return array
     */
    public function getSalesBySellers(): array
    {
        return DB::table('sales')
                    ->selectRaw('users.name AS employee, SUM(sales.amount) AS amount')
                    ->join('users', 'sales.seller_id', '=', 'users.id')
                    ->groupBy('users.name')->get()->toArray();
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function update(Request $request): bool
    {
        $hashId = $request->input('hash_id');
        $date = $request->input('date');
        $amount = $request->input('amount');

        return $this->sale->whereHashId($hashId)
            ->update([
                'date' => $date,
                'amount' => $amount
            ]);
    }

    /**
     * @param string $hashId
     * @return bool
     */
    public function exitsThisSale(string $hashId): bool
    {
        return !is_null($this->sale->whereHashId($hashId)->get());
    }

    /**
     * @param string $hashId
     * @param int $sellerId
     * @return bool
     */
    public function isThisSaleOfThisSeller(string $hashId, int $sellerId): bool
    {
        return $this->sale->whereHashId($hashId)->get()->first()->seller_id === $sellerId;
    }

    /**
     * @return array
     */
    public function getSalesOfAuthSeller(): array
    {
        return Auth::user()->mySales->toArray();
    }

    /**
     * @return array
     */
    public function getPurchasesOfAuthBuyer(): array
    {
        return Auth::user()->myPurchases()->with('seller')->with('property')->get()->toArray();
    }
}
