<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function setUp(): void
    {
        parent::setUp(); // Before each test

        shell_exec('php artisan migrate:fresh --seed');
        shell_exec('php artisan passport:install');
    }

    public function tearDown(): void
    {
        parent::tearDown(); // After each test

//        shell_exec('php artisan migrate:fresh --seed');
//        shell_exec('php artisan passport:install');
    }

    public function getRoleTokenAuth(string $role)
    {
        $baseUrl = Config::get('app.url') . '/api/auth/login';
        $email = Config::get('api.api'.ucfirst($role).'Email');
        $password = Config::get('api.api'.ucfirst($role).'Password');

        $response = $this->postJson($baseUrl, [
            'email' => $email,
            'password' => $password
        ]);

        $this->assertAuthenticated();

        return $response['dataUser']['accessToken'];
    }

    public function logOutUser(string $token)
    {
        $uri = Config::get('app.url') . '/api/auth/logout';

        $response = $this
            ->withHeader('Authorization','Bearer ' . $token)
            ->getJson($uri, );

        $response
            ->assertStatus(Response::HTTP_NO_CONTENT)
            ->assertJson([
                'success' => true,
                'message' => 'Successfully logged out'
            ]);
    }
}
