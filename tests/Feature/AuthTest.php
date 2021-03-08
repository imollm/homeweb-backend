<?php

namespace Tests\Feature;

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

        $response = $this->json('POST', $baseUrl . '/', [
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
        $email = Config::get('api.apiCustomerEmail');
        $password = Config::get('api.apiCustomerPassword');

        $response = $this->json('POST', $baseUrl . '/', [
            'email' => $email,
            'password' => $password
        ]);

        $response
            ->assertStatus(Response::HTTP_OK)
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
        $baseUrl =  Config::get('app.url') . '/api/auth/login';
        $email =    Config::get('api.apiEmployeeEmail');
        $password = Config::get('api.apiEmployeePassword');

        $response = $this->json('POST', $baseUrl . '/', [
            'email' => $email,
            'password' => $password
        ]);

        $response
            ->assertStatus(Response::HTTP_OK)
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
        $baseUrl =  Config::get('app.url') . '/api/auth/login';
        $email =    Config::get('api.apiOwnerEmail');
        $password = Config::get('api.apiOwnerPassword');

        $response = $this->json('POST', $baseUrl . '/', [
            'email' => $email,
            'password' => $password
        ]);

        $response
            ->assertStatus(Response::HTTP_OK)
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
        $loginUrl =     Config::get('app.url') . '/api/auth/login';
        $urlLogout =    Config::get('app.url') . '/api/auth/logout';
        $email =        Config::get('api.apiOwnerEmail');
        $password =     Config::get('api.apiOwnerPassword');

        $response = $this->postJson( $loginUrl . '/', [
            'email' => $email,
            'password' => $password
        ]);

        $token = $response['dataUser']['accessToken'];

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'dataUser' => [
                    'id' => 3,
                    'name' => 'Owner',
                    'email' => 'owner@homeweb.com',
                    'accessToken' => $token,
                    'role' => 'owner',
                ]
            ]);

        $response = $this
            ->withHeader('Authorization', 'Bearer '. $token)
            ->withHeader('Accept', 'application/json')
            ->getJson($urlLogout);

        $response
            ->assertStatus(Response::HTTP_OK)
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
        $loginUrl =     Config::get('app.url') . '/api/auth/login';
        $authUserUrl =  Config::get('app.url') . '/api/auth/user';
        $email = Config::get('api.apiAdminEmail');
        $password = Config::get('api.apiAdminPassword');

        $loginResponse = $this->json('POST', $loginUrl . '/', [
            'email' => $email,
            'password' => $password
        ]);

        $token = $loginResponse['dataUser']['accessToken'];

        $loginResponse
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'dataUser' => [
                    'id' => 2,
                    'name' => 'Admin',
                    'email' => 'admin@homeweb.com',
                    'accessToken' => $token,
                    'role' => 'admin',
                ]
            ]);

        $authUserResponse = $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($authUserUrl);

        $authUserResponse
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(['success' => true, 'message' => 'Auth user']);
    }
}
