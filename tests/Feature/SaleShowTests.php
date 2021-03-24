<?php

namespace Tests\Feature;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SaleShowTests extends TestCase
{
    public function test_sale_index_admin_role()
    {
        $token = $this->getRoleTokenAuth('admin');

        $userDoAction = User::whereEmail('admin@homeweb.com')->get()->first();
        $userId = $userDoAction->id;
        $userRole = $userDoAction->role->name;

        $sales = Sale::all()->toArray();

        $uri = Config::get('app.url') . '/api/sales/index';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => $sales,
                'message' => 'All sales of user ' . $userId . ' with role ' . $userRole
            ]);
    }

    public function test_sale_index_employee_role()
    {
        $token = $this->getRoleTokenAuth('employee1');

        $userDoAction = User::whereEmail('employee1@homeweb.com')->get()->first();
        $userId = $userDoAction->id;
        $userRole = $userDoAction->role->name;

        $sales = Sale::whereSellerId($userId)->get()->toArray();

        $uri = Config::get('app.url') . '/api/sales/index';

        $response =
            $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri);

        if ($response->status() === Response::HTTP_OK) {

            $response->assertJson([
                    'success' => true,
                    'data' => $sales,
                    'message' => 'All sales of user ' . $userId . ' with role ' . $userRole
                ]);

        }
    }

    public function test_sale_index_customer_role()
    {
        $token = $this->getRoleTokenAuth('customer');

        $userDoAction = User::whereEmail('customer@homeweb.com')->get()->first();
        $userId = $userDoAction->id;
        $userRole = $userDoAction->role->name;

        $sales = Sale::whereSellerId($userId)->get()->toArray();

        $uri = Config::get('app.url') . '/api/sales/index';

        $response =
            $this
                ->withHeader('Authorization', 'Bearer ' . $token)
                ->getJson($uri);

        if ($response->status() === Response::HTTP_OK) {

            $response->assertJson([
                'success' => true,
                'data' => $sales,
                'message' => 'All sales of user ' . $userId . ' with role ' . $userRole
            ]);

        }
    }

    public function test_sale_index_owner_role()
    {
        $token = $this->getRoleTokenAuth('owner');

        $userDoAction = User::whereEmail('owner@homeweb.com')->get()->first();
        $userId = $userDoAction->id;
        $userRole = $userDoAction->role->name;

        $sales = Sale::join('properties', 'sales.property_id', '=', 'properties.id')
            ->where('properties.user_id', '=', $userId)
            ->get('sales.*')
            ->toArray();

        $uri = Config::get('app.url') . '/api/sales/index';

        $response =
            $this
                ->withHeader('Authorization', 'Bearer ' . $token)
                ->getJson($uri);

        if ($response->status() === Response::HTTP_OK) {

            $response->assertJson([
                'success' => true,
                'data' => $sales,
                'message' => 'All sales of user ' . $userId . ' with role ' . $userRole
            ]);

        }
    }

    public function test_sale_show_by_hash_id_not_found_admin_role()
    {
        $token = $this->getRoleTokenAuth('admin');

        $hashId = Sale::max('hash_id') . 'ssss';

        $uri = Config::get('app.url') . '/api/sales/'.$hashId.'/showByHashId';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'Any sale with this params'
            ]);
    }

    public function test_sale_show_by_hash_id_is_not_her_sale_employee_role()
    {
        $token = $this->getRoleTokenAuth('employee');

        $ownerId = User::whereName('Owner')->get()->first()->id;
        $saleRelatedWithAnotherOwner = Sale::where('seller_id', '<>', $ownerId)->get()->first()->hash_id;

        $uri = Config::get('app.url') . '/api/sales/'.$saleRelatedWithAnotherOwner.'/showByHashId';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'Any sale with this params'
            ]);
    }

    public function test_sale_show_by_hash_id_is_not_her_sale_customer_role()
    {
        $token = $this->getRoleTokenAuth('customer');

        $customerId = User::whereName('Customer')->get()->first()->id;
        $saleRelatedWithAnotherOCustomer =
            Sale::where('buyer_id', '<>', $customerId)->get()->first()->hash_id;

        $uri = Config::get('app.url') . '/api/sales/'.$saleRelatedWithAnotherOCustomer.'/showByHashId';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'Any sale with this params'
            ]);
    }

    public function test_sale_show_by_hash_id_is_not_her_sale_owner_role()
    {
        $token = $this->getRoleTokenAuth('owner');

        $ownerId = User::whereName('Owner')->get()->first()->id;
        $saleRelatedWithAnotherOwner =
            Sale::join('properties', 'sales.property_id', '=', 'properties.id')
                ->join('users', 'properties.user_id', '=', 'users.id')
                ->where('users.id', '<>', $ownerId)
                ->get('sales.*')
                ->first()
                ->hash_id;

        $uri = Config::get('app.url') . '/api/sales/'.$saleRelatedWithAnotherOwner.'/showByHashId';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'Any sale with this params'
            ]);
    }
}
