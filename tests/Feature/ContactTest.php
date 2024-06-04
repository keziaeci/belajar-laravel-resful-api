<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Contact;
use Database\Seeders\UserSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\ContactSeeder;
use Illuminate\Support\Facades\Log;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
            'Authorization' => '123'
        ])
        ->assertStatus(401)
        ->assertJson([
            "errors" => [
                "message" => 'unauthorized'
            ]
        ]);
    }

    function testGetShowSuccess()  {
        $this->seed([UserSeeder::class,ContactSeeder::class]);
        $con = Contact::first();

        $this->get("/api/contacts/$con->id",[
            'Authorization' => 'ren'
        ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'first_name' => 'test',
                'last_name' => 'test',
                'email' => 'test@gmail.com',
                'phone' => '123123',
            ]
        ]);
    }

    function testGetShowNotFound()  {
        $this->seed([UserSeeder::class,ContactSeeder::class]);

        $this->get("/api/contacts/0",[
            'Authorization' => 'ren'
        ])
        ->assertStatus(404)
        ->assertJson([
            "errors" => [
                "message" => [
                    "Not Found"
                ]
            ]
        ]);
    }

    function testGetOtherUserContact() {
        $this->seed([UserSeeder::class,ContactSeeder::class]);
        $con = Contact::first();

        $this->get("/api/contacts/$con->id",[
            'Authorization' => 'test'
        ])
        ->assertStatus(404)
        ->assertJson([
            "errors" => [
                "message" => [
                    "Not Found"
                ]
            ]
        ]);
    }

    function testUpdateSuccess() {
        $this->seed([UserSeeder::class,ContactSeeder::class]);
        $con = Contact::first();

        $this->put("/api/contacts/$con->id",[
            'first_name' => 'ren',
            'last_name' => 'ren',
            'email' => 'ren@gmail.com',
            'phone' => '',
        ],[
            'Authorization' => 'ren'
        ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'first_name' => 'ren',
                'last_name' => 'ren',
                'email' => 'ren@gmail.com',
                'phone' => '',
            ]
        ]);
    }

    function testUpdateValidationError() {
        $this->seed([UserSeeder::class,ContactSeeder::class]);
        $con = Contact::first();

        $this->put("/api/contacts/$con->id",[
            'first_name' => '',
            'last_name' => 'ren',
            'email' => 'ren@gmail.com',
            'phone' => '',
        ],[
            'Authorization' => 'ren'
        ])
        ->assertStatus(400)
        ->assertJson([
            "errors" => [
                "first_name" => [
                    "The first name field is required."
                ]
            ]
        ]);
    }

    function testDeleteSuccess() {
        $this->seed([UserSeeder::class,ContactSeeder::class]);
        $con = Contact::first();

        $this->delete("/api/contacts/$con->id",[], headers:[
            'Authorization' => 'ren'
        ])
        ->assertStatus(200)
        ->assertJson([
            'data' => 'true'
        ]);
    }

    function testDeleteNotFound() {
        $this->seed([UserSeeder::class,ContactSeeder::class]);

        $this->delete("/api/contacts/0",[], headers:[
            'Authorization' => 'ren'
        ])
        ->assertStatus(404)
        ->assertJson([
            'errors' => [
                'message' => [
                    'Not Found'
                ]
            ]
        ]);
    }

    function testSearchByFirstName() {
        $this->seed([UserSeeder::class,SearchSeeder::class]);
        
        $res = $this->get('/api/contacts?name=first', [
            'Authorization' => 'ren'
        ])
        ->assertStatus(200)
        ->json();

        // dd($res);
        Log::info(json_encode($res,JSON_PRETTY_PRINT));
        assertCount(10,$res['data']);
        assertEquals(20,$res['meta']['total']);

    }

    function testSearchByLastName() {
        $this->seed([UserSeeder::class,SearchSeeder::class]);
        
        $res = $this->get('/api/contacts?name=last', [
            'Authorization' => 'ren'
        ])
        ->assertStatus(200)
        ->json();

        // dd($res);
        Log::info(json_encode($res,JSON_PRETTY_PRINT));
        assertCount(10,$res['data']);
        assertEquals(20,$res['meta']['total']);
    }

    function testSearchByEmail() {
        $this->seed([UserSeeder::class,SearchSeeder::class]);
        
        $res = $this->get('/api/contacts?email=@', [
            'Authorization' => 'ren'
        ])
        ->assertStatus(200)
        ->json();

        // dd($res);
        Log::info(json_encode($res,JSON_PRETTY_PRINT));
        assertCount(10,$res['data']);
        assertEquals(20,$res['meta']['total']);
    }

    function testSearchByPhone() {
        $this->seed([UserSeeder::class,SearchSeeder::class]);
        
        $res = $this->get('/api/contacts?phone=1', [
            'Authorization' => 'ren'
        ])
        ->assertStatus(200)
        ->json();

        Log::info(json_encode($res,JSON_PRETTY_PRINT));
        assertCount(10,$res['data']);
        assertEquals(20,$res['meta']['total']);
    }

    function testSearchNotFound() {
        $this->seed([UserSeeder::class,SearchSeeder::class]);
        
        $res = $this->get('/api/contacts?name=12312312', [
            'Authorization' => 'ren'
        ])
        ->assertStatus(200)
        ->json();

        Log::info(json_encode($res,JSON_PRETTY_PRINT));
        assertCount(0,$res['data']);
        assertEquals(0,$res['meta']['total']);
    }

    function testSearchWithPage() {
        $this->seed([UserSeeder::class,SearchSeeder::class]);
        
        $res = $this->get('/api/contacts?size=5&page=2', [
            'Authorization' => 'ren'
        ])
        ->assertStatus(200)
        ->json();

        Log::info(json_encode($res,JSON_PRETTY_PRINT));
        assertCount(5,$res['data']);
        assertEquals(20,$res['meta']['total']);
        assertEquals(2,$res['meta']['current_page']);
    }
}
