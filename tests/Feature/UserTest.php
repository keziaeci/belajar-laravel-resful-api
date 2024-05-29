<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;

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

    function testGetCurrentUser() {
        $this->seed([UserSeeder::class]);
        
        $this->get('/api/users/current',[
            'Authorization' => 'ren'
        ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'username' => 'ren',
                'name' => 'Regina',
            ]
        ]);
    }

    function testGetCurrentUserUnauthorized() {
        $this->seed([UserSeeder::class]);
        
        $this->get('/api/users/current')
        ->assertStatus(401)
        ->assertJson([
            'errors' => [
                'message' => 'unauthorized',
            ]
        ]);
    }

    function testGetCurrentUserInvalidToken() {
        $this->seed([UserSeeder::class]);
        
        $this->get('/api/users/current',[
            'Authorization' => 'asdu'
        ])
        ->assertStatus(401)
        ->assertJson([
            'errors' => [
                'message' => 'unauthorized',
            ]
        ]);
    }

    function testUpdateNameSuccess() {
        $this->seed([UserSeeder::class]);
        
        $this->patch('/api/users/current',[
            'name' => 'aniger'
        ],
        [
            'Authorization' => 'ren'
        ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'username' => 'ren',
                'name' => 'aniger',
                ]
        ]);
    }

    function testUpdatePasswordSuccess() {
        $this->seed([UserSeeder::class]);
        
        $this->patch('/api/users/current',[
            'password' => 'ASD'
        ],
        [
            'Authorization' => 'ren'
        ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'username' => 'ren',
                'name' => 'Regina',
                ]
        ]);
    }

    function testUpdateFailed() {
        $this->seed([UserSeeder::class]);
        
        $this->patch('/api/users/current',[
            'name' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Ex sit cumque quidem maxime, sunt cupiditate porro sapiente minima? Quo expedita repellat, non ullam harum sed aliquam tempore beatae esse odit, aliquid vero quas! Error quibusdam dicta quam. Eos, incidunt provident. Totam iure hic animi iusto tempore quidem aperiam veniam omnis eveniet id enim inventore, consequuntur cum porro libero natus deserunt corrupti aspernatur molestias quisquam recusandae aut voluptatibus. Possimus, exercitationem omnis explicabo voluptatum eligendi saepe pariatur, consequuntur non repellat delectus expedita earum? Ut sequi labore architecto tenetur vitae, rerum asperiores cumque ipsam sapiente omnis perferendis molestias quisquam, consequuntur repellendus eum quas?'
        ],
        [
            'Authorization' => 'ren'
        ])
        ->assertStatus(400)
        ->assertJson([
            'errors' => [
                'name' => [
                    'The name field must not be greater than 100 characters.'
                ]
            ]
        ]);
    }

    function testLogoutSuccess() {
        $this->seed(UserSeeder::class);

        $this->delete('/api/users/logout',headers: [
            'Authorization' => 'ren'
        ])
        ->assertStatus(200)
        ->assertJson([
            'data' => true
        ]);

        $user = User::where('username','ren')->first();
        // assertEquals(null,$user->token);
        assertNull($user->token);
    }

    function testLogoutFailed() {
        $this->seed(UserSeeder::class);

        $this->delete('/api/users/logout',headers: [
            'Authorization' => 'asfdasdf'
        ])
        ->assertStatus(401)
        ->assertJson([
            'errors' => [
                'message' => 'unauthorized'
            ]
        ]);
    }
}