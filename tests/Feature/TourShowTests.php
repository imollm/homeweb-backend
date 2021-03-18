<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TourShowTests extends TestCase
{
    /**
     * INDEX, RETRIEVE ALL TOURS, ROLES AUTHORIZED AND UNAUTHORIZED
     */
    public function test_tour_show_index_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/tours/index';

        $allTours = Tour::all()->toArray();

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => $allTours,
                'message' => 'All tours'
            ]);
    }

    public function test_tour_show_index_customer_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $uri = Config::get('app.url') . '/api/tours/index';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_tour_show_index_employee_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('employee');

        $uri = Config::get('app.url') . '/api/tours/index';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_tour_show_index_owner_role_unauthorized()
    {
        $token = $this->getRoleTokenAuth('owner');

        $uri = Config::get('app.url') . '/api/tours/index';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * RETRIEVE A TOUR BY HASH ID - HASH(PROPERTY_ID + CUSTOMER_ID + EMPLOYEE_ID + DATE + TIME)
     * BECAUSE TOUR TABLE NOT HAVE A PRIMARY KEY ID
     */
    public function test_tour_show_by_hash_id_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('owner');

        $tour = Tour::inRandomOrder()->first();
        $propertyId = $tour->property_id;
        $customerId = $tour->customer_id;
        $employeeId = $tour->employee_id;
        $date = $tour->date;
        $time = $tour->time;

        $hashId = hash("sha256", $propertyId.$customerId.$employeeId.$date.$time);

        $uri = Config::get('app.url') . '/api/tours/'.$hashId.'/showByHashId';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => [
                    'property_id' => $propertyId,
                    'customer_id' => $customerId,
                    'employee_id' => $employeeId,
                    'date' => $date,
                    'time' => $time,
                    'hash_id' => $hashId
                ],
                'message' => 'Tour by hash id ' . $hashId
            ]);
    }

    /**
     * PROPERTY NOT FOUND
     */
    public function test_tour_show_by_property_id_not_found_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $propertyIdNotExists = Property::max('id') + 1;

        $uri = Config::get('app.url') . '/api/tours/property/'.$propertyIdNotExists.'/show';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'Property not found'
            ]);
    }

    public function test_tour_show_by_property_id_not_found_employee_role_authorized()
    {
        $token = $this->getRoleTokenAuth('employee');

        $propertyIdNotExists = Property::max('id') + 1;

        $uri = Config::get('app.url') . '/api/tours/property/'.$propertyIdNotExists.'/show';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'Property not found'
            ]);
    }

    public function test_tour_show_by_property_id_not_found_customer_role_authorized()
    {
        $token = $this->getRoleTokenAuth('customer');

        $propertyIdNotExists = Property::max('id') + 1;

        $uri = Config::get('app.url') . '/api/tours/property/'.$propertyIdNotExists.'/show';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'Property not found'
            ]);
    }

    public function test_tour_show_by_property_id_not_found_owner_role_authorized()
    {
        $token = $this->getRoleTokenAuth('owner');

        $propertyIdNotExists = Property::max('id') + 1;

        $uri = Config::get('app.url') . '/api/tours/property/'.$propertyIdNotExists.'/show';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
                'message' => 'Property not found'
            ]);
    }

    /**
     * RETRIEVE BY PROPERTY ID
     */
    public function test_tour_show_by_property_id_admin_role_authorized()
    {
        $token = $this->getRoleTokenAuth('admin');

        $propertyIdExists = Property::inRandomOrder()->first()->id;

        $uri = Config::get('app.url') . '/api/tours/property/'.$propertyIdExists.'/show';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => array(),
                'message' => 'All tours of property ' . $propertyIdExists
            ]);
    }

    public function test_tour_show_by_property_id_owner_role_property_is_not_yours()
    {
        $token = $this->getRoleTokenAuth('owner');

        $ownerDoActionName = 'Owner';

        $propertyWithToursOfAnotherOwner =
            Property::join('users', 'users.id', '=', 'properties.user_id')
                ->join('tours', 'tours.property_id', '=', 'properties.id')
                ->where('users.name', '<>', $ownerDoActionName)
                ->get()
                ->pluck('property_id')
                ->first();

        $uri = Config::get('app.url') . '/api/tours/property/'.$propertyWithToursOfAnotherOwner.'/show';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson([
                'success' => false,
                'message' => 'The property is not yours'
            ]);
    }

    public function test_tour_show_by_property_id_owner_role_property_is_yours()
    {
        $token = $this->getRoleTokenAuth('owner');

        $ownerDoActionName = 'Owner';

        $myPropertyWithTours =
            Property::join('users', 'users.id', '=', 'properties.user_id')
                ->join('tours', 'tours.property_id', '=', 'properties.id')
                ->where('users.name', '=', $ownerDoActionName)
                ->get()
                ->pluck('property_id')
                ->first();

        $toursExpected =
            Tour::wherePropertyId($myPropertyWithTours)
                ->get()
                ->toArray();

        $uri = Config::get('app.url') . '/api/tours/property/'.$myPropertyWithTours.'/show';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => $toursExpected,
                'message' => 'All tours of your property with id ' . $myPropertyWithTours
            ]);
    }

    public function test_tour_show_by_property_id_customer_role_have_not_visits_with_this_property()
    {
        $token = $this->getRoleTokenAuth('customer');

        $customerDoActionName = 'Customer';

        $iHaveNoVisitWithThisProperty =
            User::join('tours', 'users.id', '=', 'tours.customer_id')
                ->where('users.name', '<>', $customerDoActionName)
                ->get()
                ->pluck('property_id')
                ->first();

        $uri = Config::get('app.url') . '/api/tours/property/'.$iHaveNoVisitWithThisProperty.'/show';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson([
                'success' => false,
                'message' => 'You have no tours with this property ' . $iHaveNoVisitWithThisProperty
            ]);
    }

    public function test_tour_show_by_property_id_customer_role_have_visits_with_this_property()
    {
        $token = $this->getRoleTokenAuth('customer');

        $customerDoActionName = 'Customer';
        $customerDoActionId =
            User::whereName($customerDoActionName)
                ->get()
                ->pluck('id');

        $iHaveNoToursWithThisProperty =
            User::join('tours', 'users.id', '=', 'tours.customer_id')
                ->where('users.name', '=', $customerDoActionName)
                ->get()
                ->pluck('property_id')
                ->first();

        $toursExpected =
            Tour::whereCustomerId($customerDoActionId)
                ->wherePropertyId($iHaveNoToursWithThisProperty)
                ->get()
                ->toArray();

        $uri = Config::get('app.url') . '/api/tours/property/'.$iHaveNoToursWithThisProperty.'/show';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => $toursExpected,
                'message' => 'All tours you have with property ' . $iHaveNoToursWithThisProperty
            ]);
    }

    public function test_tour_show_by_property_id_employee_role_have_not_visits_with_this_property()
    {
        $token = $this->getRoleTokenAuth('employee');

        $customerDoActionName = 'Employee';

        $iHaveNoToursWithThisProperty =
            User::join('tours', 'users.id', '=', 'tours.employee_id')
                ->where('users.name', '<>', $customerDoActionName)
                ->get()
                ->pluck('property_id')
                ->first();

        $uri = Config::get('app.url') . '/api/tours/property/'.$iHaveNoToursWithThisProperty.'/show';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson([
                'success' => false,
                'message' => 'You have no tours with this property ' . $iHaveNoToursWithThisProperty
            ]);
    }

    public function test_tour_show_by_property_id_owner_role_have_visits_with_this_property()
    {
        $token = $this->getRoleTokenAuth('employee');

        $customerDoActionName = 'Employee';
        $customerDoActionId =
            User::whereName($customerDoActionName)
                ->get()
                ->pluck('id');

        $iHaveNoToursWithThisProperty =
            User::join('tours', 'users.id', '=', 'tours.employee_id')
                ->where('users.name', '=', $customerDoActionName)
                ->get()
                ->pluck('property_id')
                ->first();

        $toursExpected =
            Tour::whereCustomerId($customerDoActionId)
                ->wherePropertyId($iHaveNoToursWithThisProperty)
                ->get()
                ->toArray();

        $uri = Config::get('app.url') . '/api/tours/property/'.$iHaveNoToursWithThisProperty.'/show';

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => $toursExpected,
                'message' => 'All tours you have with property ' . $iHaveNoToursWithThisProperty
            ]);
    }

    /**
     * RETRIEVE MY RELATED TOURS
     */
    public function test_tour_show_all_tours_admin_role()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/tours/show';

        $lastTours = Tour::orderBy('created_at', 'desc')->take(10)->get()->toArray();

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => $lastTours,
                'message' => 'Last tours'
            ]);
    }

    public function test_tour_show_all_tours_admin_role_no_tours_found()
    {
        $token = $this->getRoleTokenAuth('admin');

        $uri = Config::get('app.url') . '/api/tours/show';

        Tour::truncate(); // Delete all tours before asserting

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('tours', []);
    }

    public function test_tour_show_all_related_tours_customer_role()
    {
        $token = $this->getRoleTokenAuth('customer');

        $uri = Config::get('app.url') . '/api/tours/show';

        $customerName = 'Customer';

        $customerId = User::whereName($customerName)->pluck('id')->first();

        $toursOfCustomer =
            Tour::join('users', 'users.id', '=', 'tours.customer_id')
                ->where('users.name', '=', $customerName)
                ->orderBy('created_at', 'desc')
                ->get('tours.*')
                ->toArray();

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => $toursOfCustomer,
                'message' => 'All tours by customer ' . $customerId
            ]);
    }

    public function test_tour_show_all_related_tours_customer_role_no_tours_found()
    {
        $token = $this->getRoleTokenAuth('customer');

        $uri = Config::get('app.url') . '/api/tours/show';

        $customerName = 'Customer';

        $customerId = User::whereName($customerName)->pluck('id')->first();

        Tour::whereCustomerId($customerId)->delete();

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_tour_show_all_related_tours_owner_role()
    {
        $token = $this->getRoleTokenAuth('owner');

        $uri = Config::get('app.url') . '/api/tours/show';

        $ownerName = 'Owner';

        $ownerId = User::whereName($ownerName)->pluck('id')->first();

        $toursOfPropertiesOwnedByOwner =
            Property::join('tours', 'properties.id', '=', 'tours.property_id')
                ->join('users', 'properties.user_id', '=', 'users.id')
                ->where('users.name', '=', $ownerName)
                ->orderBy('created_at', 'desc')
                ->get('tours.*')
                ->toArray();

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => $toursOfPropertiesOwnedByOwner,
                'message' => 'All tours of owner ' . $ownerId . ' properties'
            ]);
    }

    public function test_tour_show_all_related_tours_owner_role_no_tours_found()
    {
        $token = $this->getRoleTokenAuth('owner');

        $uri = Config::get('app.url') . '/api/tours/show';

        $ownerName = 'Owner';

        $arrayOfToursToDelete =
            Tour::join('properties', 'tours.property_id', '=', 'properties.id')
                    ->join('users', 'users.id', '=', 'properties.user_id')
                    ->where('users.name', '=', $ownerName)
                    ->get('tours.hash_id')
                    ->toArray();

        Tour::whereHashId($arrayOfToursToDelete)->delete();

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_tour_show_all_related_tours_employee_role()
    {
        $token = $this->getRoleTokenAuth('employee');

        $uri = Config::get('app.url') . '/api/tours/show';

        $employeeName = 'Employee';

        $employeeId = User::whereName($employeeName)->pluck('id')->first();

        $toursOfEmployee =
            Tour::join('users', 'users.id', '=', 'tours.employee_id')
                ->where('users.name', '=', $employeeName)
                ->orderBy('created_at', 'desc')
                ->get('tours.*')
                ->toArray();

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => $toursOfEmployee,
                'message' => 'All tours by employee ' . $employeeId
            ]);
    }

    public function test_tour_show_all_related_tours_employee_role_no_tours_found()
    {
        $token = $this->getRoleTokenAuth('employee');

        $uri = Config::get('app.url') . '/api/tours/show';

        $employeeName = 'Employee';

        $employeeId = User::whereName($employeeName)->pluck('id')->first();

        Tour::whereEmployeeId($employeeId)->delete();

        $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($uri)
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
