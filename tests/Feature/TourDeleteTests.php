<?php

namespace Tests\Feature;

use App\Models\Tour;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TourDeleteTests extends TestCase
{
    public function test_tour_delete_owner_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('owner');

        $randHashIdTour = Tour::inRandomOrder()->get()->first()->hash_id;

        $uri = Config::get('app.url') . '/api/tours/' .$randHashIdTour. '/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_tour_delete_admin_role_tour_not_found()
    {
        $token = $this->getRoleTokenAuth('admin');

        $tourNotExists = Tour::inRandomOrder()->get()->first()->hash_id . 'sdkfj';

        $uri = Config::get('app.url') . '/api/tours/' .$tourNotExists. '/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_tour_delete_customer_role_tour_not_found()
    {
        $token = $this->getRoleTokenAuth('customer');

        $tourNotExists = Tour::inRandomOrder()->get()->first()->hash_id . 'sdkfj';

        $uri = Config::get('app.url') . '/api/tours/' .$tourNotExists. '/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'Tour not found'
            ]);
    }

    public function test_tour_delete_employee_role_tour_not_found()
    {
        $token = $this->getRoleTokenAuth('employee');

        $tourNotExists = Tour::inRandomOrder()->get()->first()->hash_id . 'sdkfj';

        $uri = Config::get('app.url') . '/api/tours/' .$tourNotExists. '/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'Tour not found'
            ]);
    }

    public function test_tour_delete_customer_role_tour_not_related()
    {
        $token = $this->getRoleTokenAuth('customer');

        $anotherCustomer = 'Customer1';

        $tourNotRelatedWithThisCustomer =
            Tour::join('users', 'tours.customer_id', '=', 'users.id')
                ->where('users.name', '=', $anotherCustomer)
                ->get()
                ->first()
                ->hash_id;

        $uri = Config::get('app.url') . '/api/tours/' .$tourNotRelatedWithThisCustomer. '/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Tour is not yours'
            ]);
    }

    public function test_tour_delete_employee_role_tour_not_related()
    {
        $token = $this->getRoleTokenAuth('employee');

        $anotherEmployee = 'Employee1';

        $tourNotRelatedWithThisEmployee =
            Tour::join('users', 'tours.employee_id', '=', 'users.id')
                ->where('users.name', '=', $anotherEmployee)
                ->get()
                ->first()
                ->hash_id;

        $uri = Config::get('app.url') . '/api/tours/' .$tourNotRelatedWithThisEmployee. '/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Tour is not yours'
            ]);
    }

    public function test_tour_delete_ok_admin_role()
    {
        $token = $this->getRoleTokenAuth('admin');

        $randTour =
            Tour::inRandomOrder()
                ->get()
                ->first();

        $uri = Config::get('app.url') . '/api/tours/' .$randTour->hash_id. '/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Tour deleted'
            ]);

        $this->assertDatabaseMissing('tours', $randTour->toArray());
    }

    public function test_tour_delete_ok_customer_role()
    {
        $token = $this->getRoleTokenAuth('customer');

        $customerDoAction = 'Customer';

        $tourRelatedWithThisCustomer =
            Tour::join('users', 'tours.customer_id', '=', 'users.id')
                ->where('users.name', '=', $customerDoAction)
                ->get('tours.*')
                ->first();

        $uri = Config::get('app.url') . '/api/tours/' .$tourRelatedWithThisCustomer->hash_id. '/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Tour deleted'
            ]);

        $this->assertDatabaseMissing('tours', $tourRelatedWithThisCustomer->toArray());
    }

    public function test_tour_delete_ok_employee_role()
    {
        $token = $this->getRoleTokenAuth('employee');

        $employeeDoAction = 'Employee';

        $tourRelatedWithThisEmployee =
            Tour::join('users', 'tours.employee_id', '=', 'users.id')
                ->where('users.name', '=', $employeeDoAction)
                ->get('tours.*')
                ->first();

        $uri = Config::get('app.url') . '/api/tours/' .$tourRelatedWithThisEmployee->hash_id. '/delete';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Tour deleted'
            ]);

        $this->assertDatabaseMissing('tours', $tourRelatedWithThisEmployee->toArray());
    }
}
