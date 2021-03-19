<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\Tour;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TourUpdateTests extends TestCase
{
    public function test_tour_update_owner_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('owner');

        $uri = Config::get('app.url') . '/api/tours/update';

        $payload = [];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_tour_update_admin_role_invalid_hash_id()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/tours/update';

        $tourToUpdate = Tour::inRandomOrder()->get()->first();

        $payload = [
            'property_id' => $tourToUpdate->property_id,
            'customer_id' => $tourToUpdate->customer_id,
            'employee_id' => $tourToUpdate->employee_id,
            'date' => $tourToUpdate->date,
            'time' => $tourToUpdate->time,
            'hash_id' => ''
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_tour_update_admin_role_invalid_post_data()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/tours/update';

        $tourToUpdate = Tour::inRandomOrder()->get()->first();

        $payload = [
            'property_id' => $tourToUpdate->property_id,
            'customer_id' => $tourToUpdate->customer_id,
            'employee_id' => $tourToUpdate->employee_id,
            'date' => $tourToUpdate->date,
            'time' => '',
            'hash_id' => ''
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_tour_update_admin_role_tour_not_found()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/tours/update';

        $tourToUpdate = Tour::inRandomOrder()->get()->first();

        $newTime = "02:00:00";

        $payload = [
            'property_id' => $tourToUpdate->property_id,
            'customer_id' => $tourToUpdate->customer_id,
            'employee_id' => $tourToUpdate->employee_id,
            'date' => $tourToUpdate->date,
            'time' => $newTime,
            'hash_id' => hash('sha256', $tourToUpdate->property_id.$tourToUpdate->customer_id.$tourToUpdate->employee_id.$tourToUpdate->date.$newTime)
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'Tour not found'
            ]);
    }

    public function test_tour_update_customer_role_not_related_with_tour()
    {
        $token = $this->getRoleTokenAuth('customer');

        $uri = Config::get('app.url') . '/api/tours/update';

        $anotherCustomerName = 'Customer1';

        $tourNotRelatedWithThisCustomer =
            Tour::join('users', 'users.id', '=', 'tours.customer_id')
                ->where('users.name', '=', $anotherCustomerName)
                ->get()
                ->first();

        $payload = [
            'property_id' => $tourNotRelatedWithThisCustomer->property_id,
            'customer_id' => $tourNotRelatedWithThisCustomer->customer_id,
            'employee_id' => $tourNotRelatedWithThisCustomer->employee_id,
            'date' => $tourNotRelatedWithThisCustomer->date,
            'time' => $tourNotRelatedWithThisCustomer->time,
            'hash_id' => $tourNotRelatedWithThisCustomer->hash_id
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'This tour ' . $payload['hash_id'] . ' it is not related to you'
            ]);
    }

    public function test_tour_update_employee_role_not_related_with_tour()
    {
        $token = $this->getRoleTokenAuth('employee');

        $uri = Config::get('app.url') . '/api/tours/update';

        $anotherEmployeeName = 'Employee1';

        $tourNotRelatedWithThisEmployee =
            Tour::join('users', 'users.id', '=', 'tours.employee_id')
                ->where('users.name', '=', $anotherEmployeeName)
                ->get()
                ->first();

        $payload = [
            'property_id' => $tourNotRelatedWithThisEmployee->property_id,
            'customer_id' => $tourNotRelatedWithThisEmployee->customer_id,
            'employee_id' => $tourNotRelatedWithThisEmployee->employee_id,
            'date' => $tourNotRelatedWithThisEmployee->date,
            'time' => $tourNotRelatedWithThisEmployee->time,
            'hash_id' => $tourNotRelatedWithThisEmployee->hash_id
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'This tour ' . $payload['hash_id'] . ' it is not related to you'
            ]);
    }

    public function test_tour_update_customer_role_not_availability()
    {
        $token = $this->getRoleTokenAuth('customer');

        $uri = Config::get('app.url') . '/api/tours/update';

        $customerDoActionName = 'Customer';

        $tourRelatedWithThisCustomer =
            Tour::join('users', 'users.id', '=', 'tours.customer_id')
                ->where('users.name', '=', $customerDoActionName)
                ->get()
                ->first();

        $employeeHasNotAvailabilityThisDateAndTime =
            Tour::where('employee_id', '=', $tourRelatedWithThisCustomer->employee_id)
                ->where('hash_id', '<>', $tourRelatedWithThisCustomer->hash_id)
                ->get()
                ->first();

        $newDate = $employeeHasNotAvailabilityThisDateAndTime->date;
        $newTime = $employeeHasNotAvailabilityThisDateAndTime->time;

        $payload = [
            'property_id' => $tourRelatedWithThisCustomer->property_id,
            'customer_id' => $tourRelatedWithThisCustomer->customer_id,
            'employee_id' => $tourRelatedWithThisCustomer->employee_id,
            'date' => $newDate,
            'time' => $newTime,
            'hash_id' => $tourRelatedWithThisCustomer->hash_id
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson([
                'success' => false,
                'message' => 'There are not availability'
            ]);
    }

    public function test_tour_update_employee_role_not_availability()
    {
        $token = $this->getRoleTokenAuth('customer');

        $uri = Config::get('app.url') . '/api/tours/update';

        $employeeDoActionName = 'Employee';

        $tourRelatedWithThisEmployee =
            Tour::join('users', 'users.id', '=', 'tours.employee_id')
                ->where('users.name', '=', $employeeDoActionName)
                ->get()
                ->first();

        $customerHasNotAvailabilityThisDateAndTime =
            Tour::where('customer_id', '=', $tourRelatedWithThisEmployee->customer_id)
                ->where('hash_id', '<>', $tourRelatedWithThisEmployee->hash_id)
                ->get()
                ->first();

        $newDate = $customerHasNotAvailabilityThisDateAndTime->date;
        $newTime = $customerHasNotAvailabilityThisDateAndTime->time;

        $payload = [
            'property_id' => $tourRelatedWithThisEmployee->property_id,
            'customer_id' => $tourRelatedWithThisEmployee->customer_id,
            'employee_id' => $tourRelatedWithThisEmployee->employee_id,
            'date' => $newDate,
            'time' => $newTime,
            'hash_id' => $tourRelatedWithThisEmployee->hash_id
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson([
                'success' => false,
                'message' => 'There are not availability'
            ]);
    }

    public function test_tour_update_ok_customer_role()
    {
        $token = $this->getRoleTokenAuth('customer');

        $uri = Config::get('app.url') . '/api/tours/update';

        $customerDoActionName = 'Customer';

        $tourRelatedWithThisCustomer =
            Tour::join('users', 'users.id', '=', 'tours.customer_id')
                ->where('users.name', '=', $customerDoActionName)
                ->get()
                ->first();

        $newDate = '2021-06-08';
        $newTime = '07:00:00';

        $payload = [
            'property_id' => $tourRelatedWithThisCustomer->property_id,
            'customer_id' => $tourRelatedWithThisCustomer->customer_id,
            'employee_id' => $tourRelatedWithThisCustomer->employee_id,
            'date' => $newDate,
            'time' => $newTime,
            'hash_id' => $tourRelatedWithThisCustomer->hash_id
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Tour updated successfully'
            ]);

        $payload['hash_id'] = hash("sha256", $payload['property_id'].$payload['customer_id'].$payload['employee_id'].$payload['date'].$payload['time']);

        $this->assertDatabaseHas('tours', $payload);
    }

    public function test_tour_update_ok_employee_role()
    {
        $token = $this->getRoleTokenAuth('employee');

        $uri = Config::get('app.url') . '/api/tours/update';

        $employeeDoActionName = 'Employee';

        $tourRelatedWithThisEmployee =
            Tour::join('users', 'users.id', '=', 'tours.employee_id')
                ->where('users.name', '=', $employeeDoActionName)
                ->get()
                ->first();

        $newDate = '2021-06-08';
        $newTime = '07:00:00';

        $payload = [
            'property_id' => $tourRelatedWithThisEmployee->property_id,
            'customer_id' => $tourRelatedWithThisEmployee->customer_id,
            'employee_id' => $tourRelatedWithThisEmployee->employee_id,
            'date' => $newDate,
            'time' => $newTime,
            'hash_id' => $tourRelatedWithThisEmployee->hash_id
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($uri, $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Tour updated successfully'
            ]);

        $payload['hash_id'] = hash("sha256", $payload['property_id'].$payload['customer_id'].$payload['employee_id'].$payload['date'].$payload['time']);

        $this->assertDatabaseHas('tours', $payload);
    }

}
