<?php

namespace Tests\Feature;

use App\Models\PriceHistory;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PriceHistoryCreateTests extends TestCase
{
    public function test_price_history_create_customer_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $uri = Config::get('app.url') . '/api/priceHistory/store';

        $payload = [];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized User'
            ]);
    }

    public function test_price_history_create_invalid_post_data_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/priceHistory/store';

        $payload = [
            'property_id' => '',
            'start' => '',
            'amount' => '',
            'end' => ''
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_price_history_create_same_property_same_amount_same_date_constraint_error_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/priceHistory/store';

        // First search a persisted price change on DB
        $priceChange = PriceHistory::inRandomOrder()->first();

        $payload = [
            "property_id" => $priceChange->property_id,
            "start" => $priceChange->start,
            "amount" => $priceChange->amount,
            "end" => null
        ];

        // Second create the same change of price, then CONFLICT
        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson([
                'success' => false,
                'message' => 'This property has identical price change'
            ]);
    }

    public function test_price_history_create_same_property_same_amount_same_date_constraint_error_employee_role_authorized()
    {
        $token = $this->getRoleTokenAuth('employee');

        $uri = Config::get('app.url') . '/api/priceHistory/store';

        // First search a persisted price change on DB
        $priceChange = PriceHistory::inRandomOrder()->first();

        $payload = [
            "property_id" => $priceChange->property_id,
            "start" => $priceChange->start,
            "amount" => $priceChange->amount,
            "end" => null
        ];

        // Second create the same change of price, then CONFLICT
        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson([
                'success' => false,
                'message' => 'This property has identical price change'
            ]);
    }

    public function test_price_history_create_start_date_given_is_lower_than_last_start_date_of_last_price_change_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/priceHistory/store';

        // In default dummy data on database we have the last price change of property id = 1
        // property_id = 1; start_timestamp = 2021-01-01 23:00:00; end_timestamp = null
        // We wants to create a new price change but start timestamp given is lower than 2021-01-01 23:00:00

        $startDateGiven = '2021-01-01';

        $payload = [
            "property_id" => 1,
            "start" => $startDateGiven,
            "amount" => 250000,
            "end" => null
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson([
                'success' => false,
                'message' => 'Start timestamp given is lower than last price change start timestamp'
            ]);
    }

    public function test_price_history_create_start_date_given_is_lower_than_last_start_date_of_last_price_change_employee_role_authorized()
    {
        $token = $this->getRoleTokenAuth('employee');

        $uri = Config::get('app.url') . '/api/priceHistory/store';

        // In default dummy data on database we have the last price change of property id = 1
        // property_id = 1; start_timestamp = 2021-01-01 23:00:00; end_timestamp = null
        // We wants to create a new price change but start timestamp given is lower than 2021-01-01 23:00:00

        $startDateGiven = '2021-01-01';

        $payload = [
            "property_id" => 1,
            "start" => $startDateGiven,
            "amount" => 250000,
            "end" => null
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson([
                'success' => false,
                'message' => 'Start timestamp given is lower than last price change start timestamp'
            ]);
    }

    public function test_price_history_create_owner_role_but_property_is_not_yours()
    {
        $ownerDoAction = 'owner';
        $anotherOwner = 'owner1';

        $token = $this->getRoleTokenAuth($ownerDoAction);

        $uri = Config::get('app.url') . '/api/priceHistory/store';

        $propertyIsNotYours =
            DB::table('properties')
            ->join('users', 'properties.user_id', '=', 'users.id')
            ->join('price_history', 'properties.id', '=', 'price_history.property_id')
            ->where('users.name', '=', $anotherOwner)
            ->pluck('properties.id')
            ->first();

        $payload = [
            "property_id" => $propertyIsNotYours,
            "start" => '2021-12-01',
            "amount" => 290000,
            "end" => null
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'The property is not yours'
            ]);
    }

    public function test_price_history_create_same_property_same_amount_same_date_constraint_error_owner_role_property_is_yours()
    {
        $ownerDoAction = 'owner';

        $token = $this->getRoleTokenAuth($ownerDoAction);

        $uri = Config::get('app.url') . '/api/priceHistory/store';

        $propertyIsYours =
            DB::table('properties')
                ->join('users', 'properties.user_id', '=', 'users.id')
                ->join('price_history', 'properties.id', '=', 'price_history.property_id')
                ->where('users.name', '=', $ownerDoAction)
                ->first();

        // All this payload data already exists in DB
        $payload = [
            "property_id" => $propertyIsYours->property_id,
            "start" => $propertyIsYours->start,
            "amount" => $propertyIsYours->amount,
            "end" => null
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson([
                'success' => false,
                'message' => 'This property has identical price change'
            ]);
    }

    public function test_price_history_create_owner_role_property_is_yours_but_start_date_is_lower_than_last_price_change()
    {
        $ownerDoAction = 'owner';

        $token = $this->getRoleTokenAuth($ownerDoAction);

        $uri = Config::get('app.url') . '/api/priceHistory/store';

        $propertyIsYours =
            DB::table('properties')
                ->join('users', 'properties.user_id', '=', 'users.id')
                ->join('price_history', 'properties.id', '=', 'price_history.property_id')
                ->where('users.name', '=', $ownerDoAction)
                ->first();

        $payload = [
            "property_id" => $propertyIsYours->property_id,
            "start" => $propertyIsYours->start, // The change I will do, have the same start date of the last change price registered
            "amount" => 300000,
            "end" => null
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson([
                'success' => false,
                'message' => 'Start timestamp given is lower than last price change start timestamp'
            ]);
    }

    public function test_price_history_create_ok_admin_role_start_date_is_greater_than_last_start_date()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/priceHistory/store';

        $startTimestamp = '2021-03-04'; // This timestamp is greater than last of property id 1

        $lastPriceChange = PriceHistory::wherePropertyId(1)->whereEnd(null)->get()->first();

        $valuesOfLastPriceChange = [
            "property_id" => $lastPriceChange->property_id,
            "start" => $lastPriceChange->start,
            "amount" => $lastPriceChange->amount,
            "end" => $startTimestamp
        ];

        $payload = [
            "property_id" => 1,
            "start" => $startTimestamp,
            "amount" => 250000,
            "end" => null
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
                'message' => 'Price change created'
            ]);

        $this->assertDatabaseHas('price_history', $valuesOfLastPriceChange);
        $this->assertDatabaseHas('price_history', $payload);
    }

    public function test_price_history_create_ok_employee_role_start_date_is_greater_than_last_start_date()
    {
        $token = $this->getRoleTokenAuth('employee');

        $uri = Config::get('app.url') . '/api/priceHistory/store';

        $lastPriceChange = PriceHistory::wherePropertyId(1)->whereEnd(null)->get()->first();

        $valuesOfLastPriceChange = [
            "property_id" => $lastPriceChange->property_id,
            "start" => $lastPriceChange->start,
            "amount" => $lastPriceChange->amount,
            "end" => date("Y-m-d", strtotime("$lastPriceChange->start +1 day"))
        ];

        $payload = [
            "property_id" => 1,
            "start" => date("Y-m-d", strtotime("$lastPriceChange->start +1 day")), // This timestamp is greater than last of property id 1, added one day
            "amount" => 250000,
            "end" => null
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
                'message' => 'Price change created'
            ]);

        $this->assertDatabaseHas('price_history', $valuesOfLastPriceChange);
        $this->assertDatabaseHas('price_history', $payload);
    }

    public function test_price_history_create_ok_owner_role_start_date_is_greater_than_last_start_date()
    {
        $ownerDoAction = 'owner';

        $token = $this->getRoleTokenAuth($ownerDoAction);

        $uri = Config::get('app.url') . '/api/priceHistory/store';

        $propertyIsYours =
            DB::table('properties')
                ->join('users', 'properties.user_id', '=', 'users.id')
                ->join('price_history', 'properties.id', '=', 'price_history.property_id')
                ->where('users.name', '=', $ownerDoAction)
                ->whereNull('price_history.end')
                ->first();

        $valuesOfLastPriceChange = [
            "property_id" => $propertyIsYours->property_id,
            "start" => $propertyIsYours->start,
            "amount" => $propertyIsYours->amount,
            "end" => date("Y-m-d", strtotime("$propertyIsYours->start +1 day")) // I close last price change with one plus day of the start date
        ];

        $payload = [
            "property_id" => $propertyIsYours->property_id,
            "start" => date("Y-m-d", strtotime("$propertyIsYours->start +1 day")), // I create a new price change with one plus day
            "amount" => 350000,
            "end" => null
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
                'message' => 'Price change created'
            ]);

        $this->assertDatabaseHas('price_history', $valuesOfLastPriceChange);
        $this->assertDatabaseHas('price_history', $payload);
    }
}
