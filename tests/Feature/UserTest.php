<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function PHPUnit\Framework\assertNotNull;

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

    function testLoginSuccess() {
        $this->seed([UserSeeder::class]);
        
        $this->post('/api/users/login',[
            'username' => 'ren',
            'password' => '123',
        ])->assertStatus(200)
        ->assertJson([
            'data' => [
                'username' => 'ren',
                'name' => 'Regina',
            ]
        ]);

        $user = User::where('username','ren')->first();
        assertNotNull($user->token);
    }

    function testLoginFailedUsernameNotFound() {
        $this->seed([UserSeeder::class]);
        
        $this->post('/api/users/login',[
            'username' => '2123',
            'password' => '123',
        ])->assertStatus(401)
        ->assertJson([
            'errors' => [
                'message' => 
                    'username or password wrong'
            ]
        ]);
    }

    function testLoginFailedWrongPassword() {
        $this->seed([UserSeeder::class]);
        
        $this->post('/api/users/login',[
            'username' => 'ren',
            'password' => 'sadf',
        ])->assertStatus(401)
        ->assertJson([
            'errors' => [
                'message' => 'username or password wrong'
            ]
        ]);
    }
}
