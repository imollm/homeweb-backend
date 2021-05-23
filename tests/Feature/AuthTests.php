<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * Class AuthTest
 * @package Tests\Feature
 */
class AuthTests extends TestCase
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
                'success' => true,
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
                'success' => true,
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
                'success' => true,
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
     * Test Employee role login
     *
     * @return void
     */
    public function test_login_owner1()
    {
        $baseUrl =  Config::get('app.url') . '/api/auth/login';
        $email =    Config::get('api.apiOwner1Email');
        $password = Config::get('api.apiOwner1Password');

        $response = $this->json('POST', $baseUrl, [
            'email' => $email,
            'password' => $password
        ]);

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'dataUser' => [
                    'id' => 5,
                    'name' => 'Owner1',
                    'email' => 'owner1@homeweb.com',
                    'accessToken' => $response['dataUser']['accessToken'],
                    'role' => 'owner',
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
                'success' => true,
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

        $responseLogin = $this->postJson($loginUrl, [
            'email' => $email,
            'password' => $password
        ]);

        $this->assertAuthenticated();

        $token = $responseLogin['dataUser']['accessToken'];

        $responseLogin
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'dataUser' => [
                    'id' => 3,
                    'name' => 'Owner',
                    'email' => 'owner@homeweb.com',
                    'accessToken' => $token,
                    'role' => 'owner',
                ]
            ]);

        $responseLogout = $this
            ->withHeader('Authorization', 'Bearer '. $token)
            ->getJson($urlLogout);

        $responseLogout
            ->assertStatus(Response::HTTP_OK);
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
                'success' => true,
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

    public function test_login_admin_with_wrong_email()
    {
        $baseUrl = Config::get('app.url') . '/api/auth/login';
        $wrongEmail = 'wrong@email.com';
        $password = Config::get('api.apiAdminPassword');

        $response = $this->json('POST', $baseUrl . '/', [
            'email' => $wrongEmail,
            'password' => $password
        ]);

        $response
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertExactJson([
                'success' => false,
                'message' => 'Unauthorized User'
            ]);
    }

    public function test_login_admin_with_wrong_password()
    {
        $baseUrl = Config::get('app.url') . '/api/auth/login';
        $email = Config::get('api.apiAdminEmail');
        $wrongPassword = 'Wrong Password';

        $response = $this->json('POST', $baseUrl . '/', [
            'email' => $email,
            'password' => $wrongPassword
        ]);

        $response
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertExactJson([
                'success' => false,
                'message' => 'Unauthorized User'
            ]);
    }
}
