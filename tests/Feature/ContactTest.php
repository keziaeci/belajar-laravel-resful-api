<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{
    function testCreateSuccess() {
        $this->seed(UserSeeder::class);
        
        $this->post('/api/contacts',[
            'first_name' => 'Maria',
            'last_name' => 'Regina',
            'email' => 'ren@gmail.com',
            'phone' => '01231233',
        ],[
            'Authorization' => 'ren'
        ])
        ->assertStatus(201)
        ->assertJson([
            'data' => [
                'first_name' => 'Maria',
                'last_name' => 'Regina',
                'email' => 'ren@gmail.com',
                'phone' => '01231233',
            ]
        ]);
    }

    function testCreateFailed() {
        $this->seed(UserSeeder::class);
        
        $this->post('/api/contacts',[
            'first_name' => '',
            'last_name' => 'Regina',
            'email' => 'dad',
            'phone' => '01231233',
        ],[
            'Authorization' => 'ren'
        ])
        ->assertStatus(400)
        ->assertJson([
            "errors" => [
                "first_name" => [
                    "The first name field is required."
                ],
                "email" => [
                    "The email field must be a valid email address."
                ]
            ]
        ]);
    }

    function testCreateUnauthorized() {
        $this->seed(UserSeeder::class);
        
        $this->post('/api/contacts',[
            'first_name' => 'Maria',
            'last_name' => 'Regina',
            'email' => 'ren@gmail.com',
            'phone' => '01231233',
        ],[
            'Authorization' => 'test'
        ])
        ->assertStatus(401)
        ->assertJson([
            "errors" => [
                "message" => 'unauthorized'
            ]
        ]);
    }

}
