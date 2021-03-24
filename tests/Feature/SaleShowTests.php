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

        } else if ($response->status() === Response::HTTP_NO_CONTENT) {

            $response->assertJson([]);
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

        } else if ($response->status() === Response::HTTP_NO_CONTENT) {

            $response->assertJson([]);
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
                ->getJson($uri)->dump();

        if ($response->status() === Response::HTTP_OK) {

            $response->assertJson([
                'success' => true,
                'data' => $sales,
                'message' => 'All sales of user ' . $userId . ' with role ' . $userRole
            ]);

        } else if ($response->status() === Response::HTTP_NO_CONTENT) {

            $response->assertJson([]);
        }
    }

    public function test_sale_show_by_hash_id_admin_role()
    {

    }
}
