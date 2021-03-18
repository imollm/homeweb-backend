<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TourCreateTests extends TestCase
{
    public function test_tour_create_customer_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $uri = Config::get('app.url') . '/api/tours/store';

        $payload = [
            'property_id' => '',
            'customer_id' => '',
            'employee_id' => '',
            'date' => '',
            'time' => ''
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_tour_create_invalid_post_data_admin_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/tours/store';

        $payload = [
            'property_id' => 1,
            'customer_id' => 2,
            'employee_id' => 3,
            'date' => '2021-03-01',
            'time' => '' // Time not send
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_tour_property_id_not_exists_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/tours/store';

        $propertyIdNotExists = Property::max('id') + 1;
        $customerIdExists = User::join('roles', 'users.role_id', '=', 'roles.id')->where('roles.name', 'customer')->pluck('users.id')->first();
        $employeeIdExists = User::join('roles', 'users.role_id', '=', 'roles.id')->where('roles.name', 'employee')->pluck('users.id')->first();

        $payload = [
            'property_id' => $propertyIdNotExists,
            'customer_id' => $customerIdExists,
            'employee_id' => $employeeIdExists,
            'date' => '2021-03-01',
            'time' => '12:00:00' // Time not send
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_tour_create_customer_id_not_exists_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/tours/store';

        $propertyIdExists = Property::max('id');
        $customerIdNotExists = User::join('roles', 'users.role_id', '=', 'roles.id')->where('roles.name', 'employee')->pluck('users.id')->first();
        $employeeIdExists = User::join('roles', 'users.role_id', '=', 'roles.id')->where('roles.name', 'employee')->pluck('users.id')->first();

        $payload = [
            'property_id' => $propertyIdExists,
            'customer_id' => $customerIdNotExists,
            'employee_id' => $employeeIdExists,
            'date' => '2021-03-01',
            'time' => '12:00:00'
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_tour_create_employee_id_not_exists_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/tours/store';

        $propertyIdExists = Property::max('id');
        $customerIdExists = User::join('roles', 'users.role_id', '=', 'roles.id')->where('roles.name', 'customer')->pluck('users.id')->first();
        $employeeIdNotExists = User::join('roles', 'users.role_id', '=', 'roles.id')->where('roles.name', 'customer')->pluck('users.id')->first();

        $payload = [
            'property_id' => $propertyIdExists,
            'customer_id' => $customerIdExists,
            'employee_id' => $employeeIdNotExists,
            'date' => '2021-03-01',
            'time' => '12:00:00'
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_tour_create_actor_property_are_not_available_at_this_datetime_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/tours/store';

        $propertyIdExists =
            Property::inRandomOrder()
                ->pluck('id')
                ->first();
        $customerIdExists =
            User::join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', 'customer')
                ->pluck('users.id')
                ->first();
        $employeeIdExists =
            User::join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', 'employee')
                ->pluck('users.id')
                ->first();

        $dayOfTour = '2021-03-01';
        $timeOfTour = '10:00:00';

        $firstTourPayload = [
            'property_id' => $propertyIdExists,
            'customer_id' => $customerIdExists,
            'employee_id' => $employeeIdExists,
            'date' => $dayOfTour,
            'time' => $timeOfTour
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $firstTourPayload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
                'message' => 'Tour created'
            ]);

        $this->assertDatabaseHas('tours', $firstTourPayload);

        $customerIdExistsDifferentFromTheFirst =
            User::join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', 'customer')
                ->where('users.id' , '<>', $customerIdExists)
                ->pluck('users.id')
                ->first();
        $employeeIdExistsDifferentFromTheFirst =
            User::join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', 'employee')
                ->where('users.id' , '<>', $employeeIdExists)
                ->pluck('users.id')
                ->first();

        $secondTourPayload = [
            'property_id' => $propertyIdExists,
            'customer_id' => $customerIdExistsDifferentFromTheFirst,
            'employee_id' => $employeeIdExistsDifferentFromTheFirst,
            'date' => $dayOfTour,
            'time' => $timeOfTour
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $secondTourPayload)
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson([
                'success' => false,
                'message' => 'At least one actor is not available'
            ]);

    }

    public function test_tour_create_actor_customer_are_not_available_at_this_datetime_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/tours/store';

        $propertyIdExists =
            Property::inRandomOrder()
                ->pluck('id')
                ->first();
        $customerIdExists =
            User::join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', 'customer')
                ->pluck('users.id')
                ->first();
        $employeeIdExists =
            User::join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', 'employee')
                ->pluck('users.id')
                ->first();

        $dayOfTour = '2021-03-01';
        $timeOfTour = '10:00:00';

        $firstTour = [
            'property_id' => $propertyIdExists,
            'customer_id' => $customerIdExists,
            'employee_id' => $employeeIdExists,
            'date' => $dayOfTour,
            'time' => $timeOfTour
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $firstTour)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
                'message' => 'Tour created'
            ]);

        $this->assertDatabaseHas('tours', $firstTour);

        $propertyIdExistsDifferentFromTheFirst =
            Property::where('id', '<>', $propertyIdExists)
                ->pluck('id')
                ->first();
        $employeeIdExistsDifferentFromTheFirst =
            User::join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', 'employee')
                ->where('users.id' , '<>', $employeeIdExists)
                ->pluck('users.id')
                ->first();

        $secondTour = [
            'property_id' => $propertyIdExistsDifferentFromTheFirst,
            'customer_id' => $customerIdExists,
            'employee_id' => $employeeIdExistsDifferentFromTheFirst,
            'date' => $dayOfTour,
            'time' => $timeOfTour
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $secondTour)
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson([
                'success' => false,
                'message' => 'At least one actor is not available'
            ]);

    }

    public function test_tour_create_actor_employee_are_not_available_at_this_datetime_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/tours/store';

        $propertyIdExists =
            Property::inRandomOrder()
                ->pluck('id')
                ->first();
        $customerIdExists =
            User::join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', 'customer')
                ->pluck('users.id')
                ->first();
        $employeeIdExists =
            User::join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', 'employee')
                ->pluck('users.id')
                ->first();

        $dayOfTour = '2021-03-01';
        $timeOfTour = '10:00:00';

        $firstTour = [
            'property_id' => $propertyIdExists,
            'customer_id' => $customerIdExists,
            'employee_id' => $employeeIdExists,
            'date' => $dayOfTour,
            'time' => $timeOfTour
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $firstTour)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
                'message' => 'Tour created'
            ]);

        $this->assertDatabaseHas('tours', $firstTour);

        $propertyIdExistsDifferentFromTheFirst =
            Property::where('id', '<>', $propertyIdExists)
                ->pluck('id')
                ->first();
        $customerIdExistsDifferentFromTheFirst =
            User::join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', 'customer')
                ->where('users.id' , '<>', $customerIdExists)
                ->pluck('users.id')
                ->first();

        $secondTour = [
            'property_id' => $propertyIdExistsDifferentFromTheFirst,
            'customer_id' => $customerIdExistsDifferentFromTheFirst,
            'employee_id' => $employeeIdExists,
            'date' => $dayOfTour,
            'time' => $timeOfTour
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $secondTour)
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson([
                'success' => false,
                'message' => 'At least one actor is not available'
            ]);
    }

    public function test_tour_create_ok_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/tours/store';

        $propertyIdExists =
            Property::inRandomOrder()
                ->pluck('id')
                ->first();
        $customerIdExists =
            User::join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', 'customer')
                ->pluck('users.id')
                ->first();
        $employeeIdExists =
            User::join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', 'employee')
                ->pluck('users.id')
                ->first();

        $dayOfTour = '2021-03-01';
        $timeOfTour = '10:00:00';

        $payload = [
            'property_id' => $propertyIdExists,
            'customer_id' => $customerIdExists,
            'employee_id' => $employeeIdExists,
            'date' => $dayOfTour,
            'time' => $timeOfTour
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
                'message' => 'Tour created'
            ]);

        $this->assertDatabaseHas('tours', $payload);
    }

    public function test_tour_create_ok_employee_role_authorized()
    {
        $token = $this->getRoleTokenAuth('employee');

        $uri = Config::get('app.url') . '/api/tours/store';

        $propertyIdExists =
            Property::inRandomOrder()
                ->pluck('id')
                ->first();
        $customerIdExists =
            User::join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', 'customer')
                ->pluck('users.id')
                ->first();
        $employeeIdExists =
            User::join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', 'employee')
                ->pluck('users.id')
                ->first();

        $dayOfTour = '2021-03-01';
        $timeOfTour = '10:00:00';

        $payload = [
            'property_id' => $propertyIdExists,
            'customer_id' => $customerIdExists,
            'employee_id' => $employeeIdExists,
            'date' => $dayOfTour,
            'time' => $timeOfTour
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
                'message' => 'Tour created'
            ]);

        $this->assertDatabaseHas('tours', $payload);
    }

    public function test_tour_create_ok_customer_role_authorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $uri = Config::get('app.url') . '/api/tours/store';

        $propertyIdExists =
            Property::inRandomOrder()
                ->pluck('id')
                ->first();
        $customerIdExists =
            User::join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', 'customer')
                ->pluck('users.id')
                ->first();
        $employeeIdExists =
            User::join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', 'employee')
                ->pluck('users.id')
                ->first();

        $dayOfTour = '2021-03-01';
        $timeOfTour = '10:00:00';

        $payload = [
            'property_id' => $propertyIdExists,
            'customer_id' => $customerIdExists,
            'employee_id' => $employeeIdExists,
            'date' => $dayOfTour,
            'time' => $timeOfTour
        ];

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson($uri, $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
                'message' => 'Tour created'
            ]);

        $this->assertDatabaseHas('tours', $payload);
    }
}
