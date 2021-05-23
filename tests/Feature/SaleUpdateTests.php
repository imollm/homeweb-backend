<?php

namespace Tests\Feature;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SaleUpdateTests extends TestCase
{
    public function test_update_sale_customer_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $uri = Config::get('app.url') . '/api/sales/update';

        $payload = [];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_update_sale_owner_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('owner');

        $uri = Config::get('app.url') . '/api/sales/update';

        $payload = [];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_update_sale_admin_ok()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/sales/update';

        $randSale = Sale::inRandomOrder()->first();

        $payload = [
            'property_id' => $randSale->property_id,
            'buyer_id' => $randSale->buyer_id,
            'seller_id' => $randSale->seller_id,
            'date' => $randSale->date,
            'amount' => $randSale->amount + 50000, // Change amount
            'hash_id' => $randSale->hash_id
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_OK);

    }

    public function test_update_sale_employee_ok()
    {
        $randSale = Sale::inRandomOrder()->first();
        $seller = User::whereId($randSale->seller_id)->get()->first()->name;

        $token = $this->getRoleTokenAuth($seller);

        $uri = Config::get('app.url') . '/api/sales/update';

        $payload = [
            'property_id' => $randSale->property_id,
            'buyer_id' => $randSale->buyer_id,
            'seller_id' => $randSale->seller_id,
            'date' => $randSale->date,
            'amount' => $randSale->amount + 50000, // Change amount
            'hash_id' => $randSale->hash_id
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_OK);

    }

    public function test_update_sale_is_not_related_with_employee()
    {
        $randSale = Sale::inRandomOrder()->first();

        $token = $this->getRoleTokenAuth('employee1');

        $uri = Config::get('app.url') . '/api/sales/update';

        $payload = [
            'property_id' => $randSale->property_id,
            'buyer_id' => $randSale->buyer_id,
            'seller_id' => $randSale->seller_id,
            'date' => $randSale->date,
            'amount' => $randSale->amount + 50000, // Change amount
            'hash_id' => $randSale->hash_id
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);

    }
}
