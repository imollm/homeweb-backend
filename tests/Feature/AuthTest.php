<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * Test Admin role login
     *
     * @return void
     */
    public function test_login_admin()
    {
        $baseUrl = Config::get('app.url') . '/api/auth/login';
        $email = Config::get('api.apiAdminEmail');
        $password = Config::get('api.apiAdminPassword');

        $response = $this->json('POST', $baseUrl, [
           'email' => $email,
           'password' => $password
        ]);

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'dataUser' => [
                    'id' => 2,
                    'name' => 'Admin',
                    'email' => 'admin@homeweb.com',
                    'accessToken' => $response['dataUser']['accessToken'],
                    'role' => 'admin',
                ]
            ]);
    }

    /**
     * Test Customer role login
     *
     * @return void
     */
    public function test_login_customer()
    {
        $baseUrl = Config::get('app.url') . '/api/auth/login';
        $email = Config::get('api.apiEmailCustomer');
        $password = Config::get('api.apiPasswordCustomer');

        $response = $this->json('POST', $baseUrl . '/', [
            'email' => $email,
            'password' => $password
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'dataUser' => [
                    'id' => 1,
                    'name' => 'Customer',
                    'email' => 'customer@homeweb.com',
                    'accessToken' => $response['dataUser']['accessToken'],
                    'role' => 'customer',
                ]
            ]);
    }

    /**
     * Test Employee role login
     *
     * @return void
     */
    public function test_login_employee()
    {
        $baseUrl = Config::get('app.url') . '/api/auth/login';
        $email = Config::get('api.apiEmployeeEmail');
        $password = Config::get('api.apiEmployeePassword');

        $response = $this->json('POST', $baseUrl . '/', [
            'email' => $email,
            'password' => $password
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'dataUser' => [
                    'id' => 4,
                    'name' => 'Employee',
                    'email' => 'employee@homeweb.com',
                    'accessToken' => $response['dataUser']['accessToken'],
                    'role' => 'employee',
                ]
            ]);
    }

    /**
     * Test Owner role login
     *
     * @return void
     */
    public function test_login_owner()
    {
        $baseUrl = Config::get('app.url') . '/api/auth/login';
        $email = Config::get('api.apiOwnerEmail');
        $password = Config::get('api.apiOwnerPassword');

        $response = $this->json('POST', $baseUrl . '/', [
            'email' => $email,
            'password' => $password
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'dataUser' => [
                    'id' => 3,
                    'name' => 'Owner',
                    'email' => 'owner@homeweb.com',
                    'accessToken' => $response['dataUser']['accessToken'],
                    'role' => 'owner',
                ]
            ]);
    }

    /**
     * Test logout
     *
     * @return void
     */
    public function test_logout()
    {
        $urlLogout = Config::get('app.url') . '/api/auth/logout';

        $response = $this->json('GET', $urlLogout, [Auth::user()]);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'success' => true,
                'message' => 'Successfully logged out'
            ]);
    }

    /**
     * Test auth user
     *
     * @return void
     */
    public function test_auth_user()
    {
        $baseUrl =      Config::get('app.url');
        $loginUrl =     $baseUrl . '/auth/login';
        $authUserUrl =  $baseUrl . '/auth/user';
        $email =        Config::get('api.apiAdminEmail');
        $password =     Config::get('api.apiAdminPassword');

        $loginResponse = $this->json('POST', $loginUrl, [
            'email' => $email,
            'password' => $password
        ]);

        $loginResponse
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'id' => 2,
                'name' => 'Admin',
                'email' => 'admin@homeweb.com',
                'accessToken' => $loginResponse['dataUser']['accessToken'],
                'role' => 'admin',
            ]);

        $authUserResponse = $this->json('GET', $authUserUrl, []);

        $authUserResponse
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'success' => true,
                'data' => Auth::user(),
                'message' => 'Auth user'
            ]);
    }
}
