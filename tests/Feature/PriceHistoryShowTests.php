<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Services\Property\PropertyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PriceHistoryShowTests extends TestCase
{
    private PropertyService $propertyService;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->propertyService = new PropertyService();
    }

    public function test_price_history_index_customer_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $uri = Config::get('app.url') . '/api/priceHistory/index';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User'
            ]);
    }

    public function test_price_history_index_owner_role_property_unauthorized()
    {
        $token = $this->getRoleTokenAuth('owner');

        $uri = Config::get('app.url') . '/api/priceHistory/index';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User'
            ]);
    }

    public function test_price_history_show_by_property_id_customer_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $randPropertyId = Property::inRandomOrder()->first()->id;

        $uri = Config::get('app.url') . '/api/priceHistory/'.$randPropertyId.'/show';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User'
            ]);
    }

    public function test_price_history_show_by_property_id_property_not_found_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $propertyIdNotExists = Property::max('id') + 1;

        $uri = Config::get('app.url') . '/api/priceHistory/'.$propertyIdNotExists.'/show';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'Property not found'
            ]);
    }

    public function test_price_history_show_by_property_id_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $propertyIdExists =
            DB::table('properties')
                ->select('properties.*')
                ->join('price_history', 'properties.id', '=', 'price_history.property_id')
                ->first()->id;

        $uri = Config::get('app.url') . '/api/priceHistory/'.$propertyIdExists.'/show';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => $this->propertyService->getPriceHistoryOfThisProperty($propertyIdExists),
                'message' => 'Price history of property ' . $propertyIdExists
            ]);
    }

    public function test_price_history_show_by_property_id_employee_role_authorized()
    {
        $token = $this->getRoleTokenAuth('employee');

        $propertyIdExists =
            DB::table('properties')
                ->select('properties.*')
                ->join('price_history', 'properties.id', '=', 'price_history.property_id')
                ->first()->id;

        $uri = Config::get('app.url') . '/api/priceHistory/'.$propertyIdExists.'/show';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => $this->propertyService->getPriceHistoryOfThisProperty($propertyIdExists),
                'message' => 'Price history of property ' . $propertyIdExists
            ]);
    }

    public function test_price_history_show_by_property_id_owner_role_but_property_is_not_yours()
    {
        $token = $this->getRoleTokenAuth('owner');

        $ownerDoAction =
            DB::table('users')
                ->where('users.email', '=', 'owner@homeweb.com')
                ->pluck('users.id')
                ->first();

        $propertyIdIsNotYours =
            DB::table('properties')
                ->select('properties.*')
                ->where('properties.user_id', '<>', $ownerDoAction)
                ->first()->id;

        $uri = Config::get('app.url') . '/api/priceHistory/'.$propertyIdIsNotYours.'/show';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'This property is not yours'
            ]);
    }

    public function test_price_history_show_by_property_id_owner_role_and_he_is_the_owner()
    {
        $token = $this->getRoleTokenAuth('owner');

        $propertyIdOwnedByOwnerDoAction =
            DB::table('users')
                ->join('roles', 'users.role_id', '=', 'roles.id')
                ->join('properties', 'users.id', '=', 'properties.user_id')
                ->where('users.name', '=', 'owner')
                ->groupBy('users.id')
                ->pluck('users.id')
                ->first();

        $uri = Config::get('app.url') . '/api/priceHistory/'.$propertyIdOwnedByOwnerDoAction.'/show';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => $this->propertyService->getPriceHistoryOfThisProperty($propertyIdOwnedByOwnerDoAction),
                'message' => 'Price history of your property ' . $propertyIdOwnedByOwnerDoAction
            ]);
    }
}
