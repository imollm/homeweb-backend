<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SaleCreateTests extends TestCase
{
    public function test_sale_create_customer_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $uri = Config::get('app.url') . '/api/sales/store';

        $payload = [];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_sale_create_owner_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('owner');

        $uri = Config::get('app.url') . '/api/sales/store';

        $payload = [];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_sale_create_employee_role_invalid_post_data()
    {
        $token = $this->getRoleTokenAuth('employee');

        $uri = Config::get('app.url') . '/api/sales/store';

        $payload = [
            'property_id' => '',
            'buyer_id' => 1,
            'seller_id' => 1,
            'date' => '2021-08-26',
            'amount' => 300000
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_sale_create_employee_role_property_not_found()
    {
        $token = $this->getRoleTokenAuth('employee');

        $uri = Config::get('app.url') . '/api/sales/store';

        $propertyIdNotExists = Property::max('id') + 1;
        $buyerIdExists =
            User::join('roles', 'roles.id', '=', 'users.role_id')
                ->where('roles.name', '=', 'customer')
                ->pluck('users.id')
                ->first();
        $sellerIdExists =
            User::join('roles', 'roles.id', '=', 'users.role_id')
                ->where('roles.name', '=', 'employee')
                ->pluck('users.id')
                ->first();

        $payload = [
            'property_id' => $propertyIdNotExists,
            'buyer_id' => $buyerIdExists,
            'seller_id' => $sellerIdExists,
            'date' => '2021-08-26',
            'amount' => 300000
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'At least one actor is not available'
            ]);
    }

    public function test_sale_create_employee_role_buyer_not_found()
    {
        $token = $this->getRoleTokenAuth('employee');

        $uri = Config::get('app.url') . '/api/sales/store';

        $propertyIdNotExists = Property::max('id');
        $buyerIdNotExists = User::max('id') + 1;
        $sellerIdExists =
            User::join('roles', 'roles.id', '=', 'users.role_id')
                ->where('roles.name', '=', 'employee')
                ->pluck('users.id')
                ->first();

        $payload = [
            'property_id' => $propertyIdNotExists,
            'buyer_id' => $buyerIdNotExists,
            'seller_id' => $sellerIdExists,
            'date' => '2021-08-26',
            'amount' => 300000
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'At least one actor is not available'
            ]);
    }

    public function test_sale_create_admin_role_employee_not_found()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/sales/store';

        $propertyIdNotExists = Property::max('id');
        $buyerIdExists =
            User::join('roles', 'roles.id', '=', 'users.role_id')
                ->where('roles.name', '=', 'customer')
                ->pluck('users.id')
                ->first();
        $sellerIdNotExists = // Get customer user
            User::join('roles', 'roles.id', '=', 'users.role_id')
                ->where('roles.name', '=', 'customer')
                ->pluck('users.id')
                ->first();

        $payload = [
            'property_id' => $propertyIdNotExists,
            'buyer_id' => $buyerIdExists,
            'seller_id' => $sellerIdNotExists,
            'date' => '2021-08-26',
            'amount' => 300000
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'At least one actor is not available'
            ]);
    }

    public function test_sale_create_admin_role_property_was_sold()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/sales/store';

        $propertySold = Property::whereSold(1)->get()->first()->id;

        $buyerIdExists =
            User::join('roles', 'roles.id', '=', 'users.role_id')
                ->where('roles.name', '=', 'customer')
                ->pluck('users.id')
                ->first();
        $sellerIdExists =
            User::join('roles', 'roles.id', '=', 'users.role_id')
                ->where('roles.name', '=', 'employee')
                ->pluck('users.id')
                ->first();

        $payload = [
            'property_id' => $propertySold,
            'buyer_id' => $buyerIdExists,
            'seller_id' => $sellerIdExists,
            'date' => '2021-08-26',
            'amount' => 300000
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson([
                'success' => false,
                'message' => 'This property was sold'
            ]);
    }

    public function test_sale_create_ok_admin_role()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/sales/store';

        $propertyToSell = Property::whereSold(0)->first()->id;

        $buyerIdExists =
            User::join('roles', 'roles.id', '=', 'users.role_id')
                ->where('roles.name', '=', 'customer')
                ->pluck('users.id')
                ->first();
        $sellerIdExists =
            User::join('roles', 'roles.id', '=', 'users.role_id')
                ->where('roles.name', '=', 'employee')
                ->pluck('users.id')
                ->first();

        $payload = [
            'property_id' => $propertyToSell,
            'buyer_id' => $buyerIdExists,
            'seller_id' => $sellerIdExists,
            'date' => '2021-08-26',
            'amount' => 300000
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
                'message' => 'Sale created'
            ]);

        $this->assertDatabaseHas('sales', $payload);
    }
}
