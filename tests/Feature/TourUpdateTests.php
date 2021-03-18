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

    }

}
