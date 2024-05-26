<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    function testRegisterSuccess() {
        $this->post('/api/users',[
            'username' => 'renalovr',
            'password' => '123',
            'name' => 'Rena Putri',
            ])->assertStatus(201)
        ->assertJson([
            'data' => [
                'username' => 'renalovr',
                // 'password' => '123',
                'name' => 'Rena Putri',
            ]
        ]);
    }

    function testRegisterFailed() {
        $this->post('/api/users',[
            'username' => '',
            'password' => '',
            'name' => '',
            ])->assertStatus(400)
        ->assertJson([
            'errors' => [
                'username' => [
                    'The username field is required.'
                ],
                'password' => [
                    'The password field is required.'
                ],
                'name' => [
                    'The name field is required.'
                ],
            ]
        ]);
    }

    function testRegisterUsernameAlreadyExists() {
        $this->testRegisterSuccess();
        $this->post('/api/users',[
            'username' => 'renalovr',
            'password' => '123',
            'name' => 'Rena Putri',
            ])->assertStatus(400)
        ->assertJson([
            'errors' => [
                'username' => [
                    'The username has already been taken.'
                ],
            ]
        ]);
    }
}
