<?php

namespace Tests\Feature\tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CountryShowTests extends TestCase
{
    public function test_country_request_all()
    {
        $uri = Config::get('app.url') . '/api/countries/index';

        $this->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => array(),
                'message' => 'List of all countries',
            ]);
    }
}
